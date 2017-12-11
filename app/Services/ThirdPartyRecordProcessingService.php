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
use App\DataModels\RecordProcessingReportUpdate;

class ThirdPartyRecordProcessingService implements IFeedPartyProcessing {
    private $emailCache = [];
    private $emailRepo;
    private $statsRepo;
    private $emailStatusRepo;
    private $latestDataRepo;

    private $filterService;
    private $assignmentService;
    private $truthService;

    private $processingDate;
    const DELIV_LIMIT = 90;

    // TODO: switch third party repos with the commented-out repos below and clean up unneeded code

    public function __construct(EmailRepo $emailRepo, 
        AttributionLevelRepo $attributionLevelRepo, 
        FeedDateEmailBreakdownRepo $statsRepo,
        ThirdPartyEmailStatusRepo $emailStatusRepo,
        EmailAttributableFeedLatestDataRepo $latestDataRepo,
        ScheduledFilterService $filterService,
        EmailFeedAssignmentService $assignmentService,
        AttributionRecordTruthService $truthService) {

        $this->emailRepo = $emailRepo;
        $this->attributionLevelRepo = $attributionLevelRepo;
        $this->statsRepo = $statsRepo;
        $this->processingDate = Carbon::today()->format('Y-m-d');
        $this->latestDataRepo = $latestDataRepo;
        $this->emailStatusRepo = $emailStatusRepo;

        $this->filterService = $filterService;
        $this->assignmentService = $assignmentService;
        $this->truthService = $truthService;
    }

    public function processPartyData(array $records, RecordProcessingReportUpdate $reportUpdate) {
        $recordsToFlag = [];

        foreach ($records as $record) {
            $currentAttributedFeedId = $this->emailRepo->getCurrentAttributedFeedId($record->emailId);
            $lastActionType = $this->emailStatusRepo->getActionStatus($record->emailId);
            $record = $this->setRecordStatus($record, $currentAttributedFeedId, $lastActionType);

            if ('unique' === $record->uniqueStatus) {
                $this->emailStatusRepo->batchInsertNew($record->mapToEmailFeedAction(ThirdPartyEmailStatus::DELIVERABLE));
                $recordsToFlag[] = $record->mapToNewRecords();

                // Update the attribution status of the per-feed user info store 
                if ($currentAttributedFeedId !== $record->feedId) {
                    $this->latestDataRepo->setAttributionStatus($record->emailId, $currentAttributedFeedId, EmailAttributableFeedLatestData::LOST_ATTRIBUTION);
                }
            }

            $reportUpdate->incrementUniqueStatus($record);

            // Update record per-feed data for all records that are not currently attributed to the same feed
            if ('duplicate' !== $record->uniqueStatus) {
                $this->latestDataRepo->batchInsert($record->mapToRecordData());
            }

            if (!is_null($lastActionType) && 'None' !== $lastActionType) {
                $reportUpdate->incrementPrevResponder($record);
            }
            
        }

        $this->latestDataRepo->insertStored();
        $this->emailStatusRepo->insertStoredNew();
        $this->statsRepo->updateProcessedData($reportUpdate);

        // 2. Handle all attribution changes.
        $this->truthService->insertBulkRecords($recordsToFlag);
        $this->assignmentService->insertBulkRecords($recordsToFlag);
        $this->filterService->insertScheduleFilterBulk($recordsToFlag, self::DELIV_LIMIT);
    }

    private function setRecordStatus(ProcessingRecord &$record, $currentAttributedFeedId, $lastActionType) {
        if ($record->isSuppressed) {
            $record->status = 'suppressed';
        }
        elseif (isset($this->emailCache[$record->emailAddress])) {

            if ($record->feedId === $currentAttributedFeedId) {
                $record->uniqueStatus = 'duplicate';
                $record->attrStatus = ''; // We won't be inserting this
            }
            elseif (null === $currentAttributedFeedId 
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
            // unless this has been deliverable for > 90 days
            $this->emailCache[$record->emailAddress] = $record->feedId;
            $lastImportDate = $this->latestDataRepo->getSubscribeDate($record->emailId);

            if (null !== $currentAttributedFeedId) {
                if ('None' === $lastActionType 
                    && Carbon::parse($lastImportDate)->addDays(self::DELIV_LIMIT)->lt(Carbon::today())) {
                    // Currently a deliverable > 90 days old
                    $record->uniqueStatus = 'unique';
                    $record->attrStatus = EmailAttributableFeedLatestData::ATTRIBUTED;
                }
                else {
                    $record->uniqueStatus = $currentAttributedFeedId === $record->feedId ? 'duplicate' : 'non-unique';
                    $record->attrStatus = EmailAttributableFeedLatestData::PASSED_DUE_TO_ATTRIBUTION;
                }
            }
            else {
                // Edge case - this record came in previously, but under 1st party. Current feed is null
                $record->uniqueStatus = 'unique';
                $record->attrStatus = EmailAttributableFeedLatestData::ATTRIBUTED;
            }
            
            
        }

        return $record;
    }
}
