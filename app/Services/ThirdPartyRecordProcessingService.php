<?php

namespace App\Services;
use App\DataModels\ProcessingRecord;
use App\Repositories\EmailRepo;
use App\Repositories\AttributionLevelRepo;
use App\Repositories\RecordDataRepo;
use App\Repositories\FeedDateEmailBreakdownRepo;
use App\Services\Interfaces\IFeedPartyProcessing;
use Carbon\Carbon;
use App\Events\NewRecords;

class ThirdPartyRecordProcessingService implements IFeedPartyProcessing {
    private $emailCache = [];
    private $emailRepo;
    private $recordDataRepo;
    private $statsRepo;

    private $processingDate;

    public function __construct(EmailRepo $emailRepo, AttributionLevelRepo $attributionLevelRepo, RecordDataRepo $recordDataRepo, FeedDateEmailBreakdownRepo $statsRepo) {
        $this->emailRepo = $emailRepo;
        $this->attributionLevelRepo = $attributionLevelRepo;
        $this->recordDataRepo = $recordDataRepo;
        $this->statsRepo = $statsRepo;
        $this->processingDate = Carbon::today()->format('Y-m-d');
    }

    /**
     *      RULES FOR EMAIL STATUS AND ATTRIBUTION:
     *      All of these are for non-suppressed emails.
     *
     *      (1). If the email is brand new, set it to unique.
     *      (2). If the email was imported < 10 days ago, keep everything as is. Note imports as "duplicate"
     *          if from the same feed and "non-unique" otherwise.
     *      (3). If the email was imported > 10 days ago and an action exists, check time elapsed since the action. 
     *          If it's been > 90 days since the action, switch to the importing feed and denote as "unique". 
     *          If <= 90 days, keep with the importing feed as a "duplicate".
     *      (4). If the email was imported > 10 days ago and no action exists, 
     */

    public function processPartyData(array $records) {

        $recordsToFlag = [];
        $statuses = [];

        foreach ($records as $record) {

            $domainGroupId = $record->domainGroupId;

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
            }
            else { 
                // This is not a new email

                $record->isDeliverable = $this->recordDataRepo->getDeliverableStatus($record->emailId);
                $attributionTruths = $this->emailRepo->getAttributionTruths($record->emailId);
                $currentAttributedFeedId = $this->emailRepo->getCurrentAttributedFeedId($emailId);

                if (0 === $attributionTruths) {
                    // Guard checking whether we have attribution info or not.
                    // If not set, we need to pretend that this was attribution all along
                    $record->uniqueStatus = 'unique';
                    $recordsToFlag[] = $this->mapToNewRecords($record);
                }
                elseif ($currentAttributedFeedId == $record->feedId) {
                    // Duplicate within the feed
                    $record->uniqueStatus = 'duplicate';
                }
                // For the rest, the feeds differ, by definition
                elseif (1 === $attributionTruths->is_recent_import) {
                    // Stays with importer
                    $record->uniqueStatus = 'non-unique';
                }
                elseif (0 === $attributionTruths->is_recent_import && 1 === $attributionTruths->has_action) {
                    // Not a recent import but there is an action
                    if (1 === $attributionTruths->action_expired) {
                        // Change attribution to importer
                        $recordsToFlag[] = $this->mapToNewRecords($record);
                        $record->uniqueStatus = 'unique';
                    }
                    else {
                        // Action did not expire. Keep attribution the same
                        $record->uniqueStatus = 'non-unique';
                    }
                    
                }
                else {
                    // Not a new record, not attributed import was not recent, has no action

                    // Also perhaps some of these could be passed into the object via a join while reading.
                    $importingAttrLevel = $this->attributionLevelRepo->getLevel($record->feedId);
                    $currentAttributionLevel = $this->emailRepo->getCurrentAttributionLevel($record->emailId);

                    if ($importingAttrLevel < $currentAttributionLevel) {
                        // Importing attribution is lower (meaning greater attribution power), so switch to import
                        $record->uniqueStatus = 'unique';
                        $recordsToFlag[] = $this->mapToNewRecords($record);
                    }
                    else {
                        $record->uniqueStatus = 'non-unique';
                    }

                }
            }

            $statuses[$record->feedId][$domainGroupId][$record->uniqueStatus]++;
            $this->recordDataRepo->insert($this->transformForRecordData($record));
        }

        $this->recordDataRepo->insertStored();
        $this->statsRepo->massUpdateValidEmailStatus($statuses, $this->processingDate);

        // Handles all attribution changes
        \Event::fire(new NewRecords($recordsToFlag));
    }

    private function mapToNewRecords(ProcessingRecord $record) {
        return [
            'email_id' => $record->emailId,
            'feed_id' => $record->feedId,
            'datetime' => $record->captureDate
        ];
    }

    private function transformForRecordData(ProcessingRecord $record) {
        return [
            'email_id' => $record->emailId,
            'is_deliverable' => $record->isDeliverable,
            'first_name' => $record->firstName,
            'last_name' => $record->lastName,
            'address' => $record->address,
            'address2' => $record->address2,
            'city' => $record->city,
            'state' => $record->state,
            'zip' => $record->zip,
            'country' => $record->country,
            'gender' => $record->gender,
            'ip' => $record->ip,
            'phone' => $record->phone,
            'source_url' => $record->sourceUrl,
            'dob' => $record->dob,
            'capture_date' => $record->captureDate,
            'subscribe_date' => $this->processingDate,
            'other_fields' => $record->otherFieldsJson
        ];
    }
}