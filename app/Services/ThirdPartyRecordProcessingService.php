<?php

namespace App\Services;
use App\DataModels\ProcessingRecord;
use App\Models\ThirdPartyEmailStatus;
use App\Repositories\EmailRepo;
use App\Repositories\AttributionLevelRepo;
use App\Repositories\FeedDateEmailBreakdownRepo;
use App\Repositories\ThirdPartyEmailStatusRepo;
use App\Repositories\EmailAttributableFeedLatestDataRepo;
use App\Services\Interfaces\IFeedPartyProcessing;
use Carbon\Carbon;
use App\Events\NewRecords;
use App\Models\EmailAttributableFeedLatestData;

class ThirdPartyRecordProcessingService implements IFeedPartyProcessing {
    private $emailCache = [];
    private $emailRepo;
    private $statsRepo;
    private $emailStatusRepo;
    private $latestDataRepo;

    private $processingDate;

    // TODO: switch third party repos with the commented-out repos below and clean up unneeded code

    public function __construct(EmailRepo $emailRepo, 
        AttributionLevelRepo $attributionLevelRepo, 
        FeedDateEmailBreakdownRepo $statsRepo,
        ThirdPartyEmailStatusRepo $emailStatusRepo,
        EmailAttributableFeedLatestDataRepo $latestDataRepo) {

        $this->emailRepo = $emailRepo;
        $this->attributionLevelRepo = $attributionLevelRepo;
        $this->statsRepo = $statsRepo;
        $this->processingDate = Carbon::today()->format('Y-m-d');
        $this->latestDataRepo = $latestDataRepo;
    }

    /**
     *      RULES FOR EMAIL STATUS AND ATTRIBUTION:
     *      All of these are for non-suppressed emails. Not all of these rules (date ranges, for example) are 
     *      handled here. This is merely an overview.
     *
     *      (1). If the email is brand new, set it to unique.
     *      (2). If the email was imported < 15 days ago, keep everything as is. Note imports as "duplicate"
     *          if from the same feed and "non-unique" otherwise.
     *      (3). If the email was imported > 15 days ago and an action exists, never change attribution. 
     *          Mark as "duplicate" if coming from the currently-attributed feed, "non-unique" otherwise
     *      (4). If the email was imported > 15 days ago and no action exists, switch if the importing feed
     *          has better attribution.
     */

    public function processPartyData(array $records) {

        $recordsToFlag = [];
        $statuses = [];
        $lastEmail = '';

        foreach ($records as $record) {

            $domainGroupId = $record->domainGroupId;
            $lastEmail = $record->emailAddress;
            $currentAttributedFeedId = null;

            // Note structure
            if (!isset($statuses[$record->feedId])) {
                $statuses[$record->feedId] = [];
                $statuses[$record->feedId][$domainGroupId] = [
                    'unique' => 0,
                    'non-unique' => 0,
                    'duplicate' => 0
                ];
            }
            elseif (!isset($statuses[$record->feedId][$domainGroupId])) {
                $statuses[$record->feedId][$domainGroupId] = [
                    'unique' => 0,
                    'non-unique' => 0,
                    'duplicate' => 0
                ];
            }

            $record = $this->setRecordStatus($record);

            if ('unique' === $record->uniqueStatus) {
                $this->emailStatusRepo->batchInsert($record->mapToEmailFeedAction(ThirdPartyEmailStatus::DELIVERABLE));
                $recordsToFlag[] = $this->mapToNewRecords($record);

                // Update the attribution status of the per-feed user info store 
                if ($currentAttributedFeedId) {
                    $this->latestDataRepo->updateAttributionStatus($record->emailId, $currentAttributedFeedId, EmailAttributableFeedLatestData::LOST_ATTRIBUTION);
                }
            }

            $statuses[$record->feedId][$domainGroupId][$record->uniqueStatus]++;
            $this->latestDataRepo->batchInsert($record->mapToRecordData());
        }

        $this->latestDataRepo->insertStored();
        $this->emailStatusRepo->insertStored();
        $this->statsRepo->massUpdateValidEmailStatus($statuses);

        // Handles all attribution changes
        $jobIdentifier = '3Party-' . substr($lastEmail, 0, 1); // starting letter - so we can identify the batch
        \Event::fire(new NewRecords($recordsToFlag, $jobIdentifier));
    }


    private function mapToNewRecords(ProcessingRecord $record) {
        return [
            'email_id' => $record->emailId,
            'feed_id' => $record->feedId,
            'datetime' => $record->captureDate,
            'capture_date' => $record->captureDate
        ];
    }


    private function setRecordStatus(ProcessingRecord &$record) {
        if ($record->isSuppressed) {
            $record->status = 'suppressed';
        }
        elseif (isset($this->emailCache[$record->emailAddress])) {
            return $record;
        }
        elseif ($record->newEmail && !isset($this->emailCache[$record->emailAddress])) {
            // Brand new email. Assigning attribution and inserting record data.
            $this->emailCache[$record->emailAddress] = 1;
            $record->uniqueStatus = 'unique';
        }
        else { 
            // This is not a new email
            $record->actionStatus = $this->emailStatusRepo->getActionStatus($record->emailId);
            $attributionTruths = $this->emailRepo->getAttributionTruths($record->emailId);
            $currentAttributedFeedId = $this->emailRepo->getCurrentAttributedFeedId($record->emailId);

            if (0 === $attributionTruths) {
                // Guard checking whether we have attribution info or not.
                // If not set, we need to pretend that this was attribution all along
                $record->uniqueStatus = 'unique';
                $record->attrStatus = EmailAttributableFeedLatestData::ATTRIBUTED;
            }
            elseif ($currentAttributedFeedId == $record->feedId) {
                // Duplicate within the feed
                $record->uniqueStatus = 'duplicate';
                $record->attrStatus = EmailAttributableFeedLatestData::ATTRIBUTED;
            }
            // For the rest, the feeds differ, by definition
            elseif (1 === $attributionTruths->is_recent_import) {
                // Stays with importer
                $record->uniqueStatus = 'non-unique';
                $record->attrStatus = EmailAttributableFeedLatestData::PASSED_DUE_TO_ATTRIBUTION;
            }
            elseif (0 === $attributionTruths->is_recent_import && 1 === $attributionTruths->has_action) {
                // Not a recent import but there is an action
                $record->uniqueStatus = 'non-unique';
                $record->attrStatus = EmailAttributableFeedLatestData::PASSED_DUE_TO_RESPONDER;                
            }
            else {
                // Not a new record, not attributed import was not recent, has no action
                $importingAttrLevel = $this->attributionLevelRepo->getLevel($record->feedId);
                $currentAttributionLevel = $this->emailRepo->getCurrentAttributionLevel($record->emailId);

                $lastImportDate = $this->latestDataRepo->getSubscribeDate($record->emailId);

                if (is_null($lastImportDate) || Carbon::parse($lastImportDate)->lt(Carbon::today()->subDays(90))) {
                    // No action and it's been over 90 days, give it to the next feed that shows up
                    $record->uniqueStatus = 'unique';
                    $record->attrStatus = EmailAttributableFeedLatestData::ATTRIBUTED;
                }
                elseif (null === $currentAttributionLevel || $importingAttrLevel < $currentAttributionLevel) {
                    // Importing attribution is lower (meaning greater attribution power), so switch to import
                    $record->uniqueStatus = 'unique';
                    $record->attrStatus = EmailAttributableFeedLatestData::ATTRIBUTED;
                }
                else {
                    $record->uniqueStatus = 'non-unique';
                    $record->attrStatus = EmailAttributableFeedLatestData::PASSED_DUE_TO_ATTRIBUTION;
                }

            }
        }

        return $record;
    }
}