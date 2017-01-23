<?php

namespace App\Services;
use App\DataModels\ProcessingRecord;
use App\Repositories\EmailRepo;
use App\Repositories\AttributionLevelRepo;
use App\Repositories\RecordDataRepo;
use App\Repositories\FeedDateEmailBreakdownRepo;
use App\Repositories\EmailFeedActionRepo;
use App\Services\Interfaces\IFeedPartyProcessing;
use Carbon\Carbon;
use App\Events\NewRecords;
use App\Models\EmailFeedAction;

class ThirdPartyRecordProcessingService implements IFeedPartyProcessing {
    private $emailCache = [];
    private $emailRepo;
    private $recordDataRepo;
    private $statsRepo;
    private $emailFeedActionRepo;

    private $processingDate;

    public function __construct(EmailRepo $emailRepo, 
        AttributionLevelRepo $attributionLevelRepo, 
        RecordDataRepo $recordDataRepo, 
        FeedDateEmailBreakdownRepo $statsRepo,
        EmailFeedActionRepo $emailFeedActionRepo) {

        $this->emailRepo = $emailRepo;
        $this->attributionLevelRepo = $attributionLevelRepo;
        $this->recordDataRepo = $recordDataRepo;
        $this->statsRepo = $statsRepo;
        $this->processingDate = Carbon::today()->format('Y-m-d');
        $this->emailFeedActionRepo = $emailFeedActionRepo;
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

            if ($record->isSuppressed) {
                $record->status = 'suppressed';
            }
            elseif (isset($this->emailCache[$record->emailAddress])) {
                continue;
            }
            elseif ($record->newEmail && !isset($this->emailCache[$record->emailAddress])) {
                // Brand new email. Assigning attribution and inserting record data.
                $this->emailCache[$record->emailAddress] = 1;
                $record->uniqueStatus = 'unique';
                $record->isDeliverable = 1;
                $recordsToFlag[] = $this->mapToNewRecords($record);
                $this->emailFeedActionRepo->batchInsert($record->mapToEmailFeedAction(EmailFeedAction::DELIVERABLE));
            }
            else { 
                // This is not a new email

                $record->isDeliverable = $this->recordDataRepo->getDeliverableStatus($record->emailId);
                $attributionTruths = $this->emailRepo->getAttributionTruths($record->emailId);
                $currentAttributedFeedId = $this->emailRepo->getCurrentAttributedFeedId($record->emailId);

                if (0 === $attributionTruths) {
                    // Guard checking whether we have attribution info or not.
                    // If not set, we need to pretend that this was attribution all along
                    $record->uniqueStatus = 'unique';
                    $recordsToFlag[] = $this->mapToNewRecords($record);
                    $record->isDeliverable = 1;
                    $actionStatus = EmailFeedAction::DELIVERABLE;
                }
                elseif ($currentAttributedFeedId == $record->feedId) {
                    // Duplicate within the feed
                    $record->uniqueStatus = 'duplicate';
                    $actionData = $this->emailFeedActionRepo->getActionDateAndStatus($record->emailId, $record->feedId);
                    $actionStatus = $actionData ? $actionData->status : EmailFeedAction::DELIVERABLE;
                }
                // For the rest, the feeds differ, by definition
                elseif (1 === $attributionTruths->is_recent_import) {
                    // Stays with importer
                    $record->uniqueStatus = 'non-unique';
                    $actionStatus = EmailFeedAction::PASSED_DUE_TO_ATTRIBUTION;
                }
                elseif (0 === $attributionTruths->is_recent_import && 1 === $attributionTruths->has_action) {
                    // Not a recent import but there is an action
                    $record->uniqueStatus = 'non-unique';
                    $actionStatus = EmailFeedAction::PASSED_DUE_TO_RESPONDER;                
                }
                else {
                    // Not a new record, not attributed import was not recent, has no action
                    $importingAttrLevel = $this->attributionLevelRepo->getLevel($record->feedId);
                    $currentAttributionLevel = $this->emailRepo->getCurrentAttributionLevel($record->emailId);

                    if (null === $currentAttributionLevel || $importingAttrLevel < $currentAttributionLevel) {
                        // Importing attribution is lower (meaning greater attribution power), so switch to import
                        $record->uniqueStatus = 'unique';
                        $recordsToFlag[] = $this->mapToNewRecords($record);
                        $record->isDeliverable = 1;
                        $actionStatus = EmailFeedAction::DELIVERABLE;
                    }
                    else {
                        $record->uniqueStatus = 'non-unique';
                        $actionStatus = EmailFeedAction::PASSED_DUE_TO_ATTRIBUTION;
                    }

                }

                $this->emailFeedActionRepo->batchInsert($record->mapToEmailFeedAction($actionStatus));
            }

            $statuses[$record->feedId][$domainGroupId][$record->uniqueStatus]++;

            if ('unique' === $record->uniqueStatus) {
                $this->recordDataRepo->batchInsert($record->mapToRecordData());
            }
            
        }

        $this->recordDataRepo->insertStored();
        $this->emailFeedActionRepo->insertStored();
        $this->statsRepo->massUpdateValidEmailStatus($statuses, $this->processingDate);

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
}