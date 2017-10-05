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
    const NEXT_FREE_DAY = 10;

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
        $lastEmail = '';

        foreach ($records as $record) {
            $lastEmail = $record->emailAddress;
            $currentAttributedFeedId = $this->emailRepo->getCurrentAttributedFeedId($record->emailId);
            $lastActionType = $this->emailStatusRepo->getActionStatus($record->emailId);
            $record = $this->setRecordStatus($record);

            if ('unique' === $record->uniqueStatus) {
                $this->emailStatusRepo->batchInsert($record->mapToEmailFeedAction(ThirdPartyEmailStatus::DELIVERABLE));
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
        $this->emailStatusRepo->insertStored();
        $this->statsRepo->updateProcessedData($reportUpdate);

        // 2. Handle all attribution changes.
        $this->truthService->insertBulkRecords($recordsToFlag);
        $this->assignmentService->insertBulkRecords($recordsToFlag);
        $this->filterService->insertScheduleFilterBulk($recordsToFlag, self::NEXT_FREE_DAY);
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

    private function setRecordStatus(ProcessingRecord $record) {
        # This will have to be uncommented later
        /*
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
        */

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
            // This is not a new email
            $record->actionStatus = $this->emailStatusRepo->getActionStatus($record->emailId);
            $attributionTruths = $this->emailRepo->getAttributionTruths($record->emailId);
            $currentAttributedFeedId = $this->emailRepo->getCurrentAttributedFeedId($record->emailId);
            $lastActionDateTime = $this->emailStatusRepo->getLastActionTime($record->emailId);
            $actionLookback = 90;
            $recentAction = ($lastActionDateTime === null) ? false : (!Carbon::parse($lastActionDateTime)->lt(Carbon::today()->subDays($actionLookback)));
            $this->emailCache[$record->emailAddress] = 1;

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
            elseif (1 === $attributionTruths->recent_import || $recentAction) {
                // Stays with importer
                $record->uniqueStatus = 'non-unique';
                $record->attrStatus = EmailAttributableFeedLatestData::PASSED_DUE_TO_ATTRIBUTION;
            }
            else {
                // Not a new record, import was not recent, has no recent action
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
