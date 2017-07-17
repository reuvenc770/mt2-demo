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
        $this->emailStatusRepo = $emailStatusRepo;
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
     *      (5). If the email was imported > 90 days ago and no action exists, treat as a brand new record.
     */

    public function processPartyData(array $records) {

        $recordsToFlag = [];
        $statuses = [];
        $lastEmail = '';

        foreach ($records as $record) {

            $domainGroupId = $record->domainGroupId;
            $lastEmail = $record->emailAddress;
            $currentAttributedFeedId = $this->emailRepo->getCurrentAttributedFeedId($record->emailId);
            $filename = $record->file;
            $lastActionType = $this->emailStatusRepo->getActionStatus($record->emailId);

            // Note structure
            if (!isset($statuses[$record->feedId])) {
                $statuses[$record->feedId] = [];
            }

            if (!isset($statuses[$record->feedId][$domainGroupId])) {
                $statuses[$record->feedId][$domainGroupId] = [];
            }

            if (!isset( $statuses[$record->feedId][$domainGroupId][$filename])) {
                $statuses[$record->feedId][$domainGroupId][$filename] = [
                    'unique' => 0,
                    'non-unique' => 0,
                    'duplicate' => 0,
                    'prev_responder_count' => 0
                ];
            }

            $record = $this->setRecordStatus($record);

            if ('unique' === $record->uniqueStatus) {
                $this->emailStatusRepo->batchInsert($record->mapToEmailFeedAction(ThirdPartyEmailStatus::DELIVERABLE));
                $recordsToFlag[] = $record->mapToNewRecords();

                // Update the attribution status of the per-feed user info store 
                if ($currentAttributedFeedId !== $record->feedId) {
                    $this->latestDataRepo->setAttributionStatus($record->emailId, $currentAttributedFeedId, EmailAttributableFeedLatestData::LOST_ATTRIBUTION);
                }
            }

            $statuses[$record->feedId][$domainGroupId][$filename][$record->uniqueStatus]++;

            // Update record per-feed data for all records that are not currently attributed to the same feed
            if ('duplicate' !== $record->uniqueStatus) {
                $this->latestDataRepo->batchInsert($record->mapToRecordData());
            }

            if (!is_null($lastActionType) && 'None' !== $lastActionType) {
                $statuses[$record->feedId][$domainGroupId][$filename]['prev_responder_count']++;
            }
            
        }

        $this->latestDataRepo->insertStored();
        $this->emailStatusRepo->insertStored();
        $this->statsRepo->massUpdateValidEmailStatus($statuses);

        // Handles all attribution changes
        $jobIdentifier = '3Party-' . substr($lastEmail, 0, 1); // starting letter - so we can identify the batch
        \Event::fire(new NewRecords($recordsToFlag, $jobIdentifier));
    }

    private function setRecordStatus(ProcessingRecord &$record) {
        if ($record->isSuppressed) {
            $record->status = 'suppressed';
        }
        elseif (isset($this->emailCache[$record->emailAddress])) {
            $currentAttributedFeedId = $this->emailRepo->getCurrentAttributedFeedId($record->emailId);

            if ($record->feedId === $currentAttributedFeedId) {
                $record->uniqueStatus = 'duplicate';
                $record->attrStatus = ''; // We won't be inserting this
            }
            elseif (0 === $currentAttributedFeedId 
                && ($record->feedId === $this->emailCache[$record->emailAddress])) {
                // probably was first attributed in this very batch
                $record->uniqueStatus = 'duplicate';
                $record->attrStatus = ''; // We won't be inserting this
            }
            else {
                $record->uniqueStatus = 'non-unique';
                $record->attrStatus = EmailAttributableFeedLatestData::PASSED_DUE_TO_ATTRIBUTION;
            }
        }
        elseif ($record->newEmail && !isset($this->emailCache[$record->emailAddress])) {
            // Brand new email. Assigning attribution and inserting record data.
            $this->emailCache[$record->emailAddress] = $record->feedId;
            $record->uniqueStatus = 'unique';
            $record->attrStatus = EmailAttributableFeedLatestData::ATTRIBUTED;
        }
        else { 
            // This is not a new email. Attribution is now first come, first served (at least here)
            $currentAttributedFeedId = $this->emailRepo->getCurrentAttributedFeedId($record->emailId);
            $record->uniqueStatus = $currentAttributedFeedId === $record->feedId ? 'duplicate' : 'non-unique';
            $record->attrStatus = EmailAttributableFeedLatestData::PASSED_DUE_TO_ATTRIBUTION;
        }

        return $record;
    }
}
