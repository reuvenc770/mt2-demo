<?php

namespace App\Services;
use App\Repositories\FirstPartyRecordDataRepo;
use App\Repositories\FeedDateEmailBreakdownRepo;
use App\Repositories\EspWorkflowLogRepo;
use App\Services\EspWorkflowStepService;
use App\Services\Interfaces\IFeedPartyProcessing;
use App\Services\Interfaces\IPostingStrategy;
use App\Services\AbstractReportService;
use Carbon\Carbon;
use App\DataModels\RecordProcessingReportUpdate;
use App\Services\Interfaces\IFeedSuppression;
use App\Services\Interfaces\ISuppressionProcessingStrategy;

class FirstPartyRecordProcessingService implements IFeedPartyProcessing {
    private $espApiService;
    private $emailRepo;
    private $statsRepo;
    private $recordDataRepo;
    private $workflowLogRepo;
    private $stepsService;

    private $emailCache = [];
    private $targetId = '';
    private $feedId;
    private $workflowId = 0;
    private $processingDate;
    private $suppressors = [];
    private $suppStrategy;

    public function __construct(AbstractReportService $espApiService, 
        FeedDateEmailBreakdownRepo $statsRepo, 
        FirstPartyRecordDataRepo $recordDataRepo,
        IPostingStrategy $postingStrategy,
        EspWorkflowLogRepo $workflowLogRepo,
        EspWorkflowStepService $stepsService) {

        $this->espApiService = $espApiService;
        $this->statsRepo = $statsRepo;
        $this->recordDataRepo = $recordDataRepo;
        $this->postingStrategy = $postingStrategy;
        $this->processingDate = Carbon::today()->format('Y-m-d');
        $this->workflowLogRepo = $workflowLogRepo;
        $this->stepsService = $stepsService;
    }

    public function processPartyData(array $records, RecordProcessingReportUpdate $reportUpdate) {
        foreach($records as $record) {
            $postingRecord = $this->postingStrategy->prepareForPosting($records, $this->targetId);
            $result = $this->espApiService->addContactToLists($postingRecord);

            $this->workflowLogRepo->insert([
                'workflow_id' => $this->workflowId,
                'email_id' => $record->emailId,
                'target_list' => $this->targetId,
                'status_received' => $result,
                'binary_status' => 1, # true for now during testing. In the future, the posting strategy should be able to decode the $result and set 0 or 1 here.
            ]);

            $this->recordDataRepo->insert($record->mapToRecordData());

            $record->uniqueStatus = $this->recordDataRepo->isUnique($record->emailId, $this->feedId) ? 'unique' : 'duplicate';
            $reportUpdate->incrementUniqueStatus($record);
        }

        $this->recordDataRepo->insertStored();
        $this->statsRepo->updateProcessedData($reportUpdate);
    }

    public function setTargetId($targetId) {
        $this->targetId = $targetId;
    }

    public function setFeedId($feedId) {
        $this->feedId = $feedId;
    }

    public function setWorkflowId($workflowId) {
        $this->workflowId = $workflowId;
    }

    /**
     *  Set suppression status.
     *  This is obviously a bit more complicated than the simple per-item lookup,
     *  but hopefully this is significantly faster. 
     *  Also sends records out to all specified, matching lists.
     *  Assumes an array of ProcessingRecords
     */

    public function suppress($records) {
        $emails = [];
        $suppressed = [];
        $finalRecords = [];
        $espTargetAssoc = $this->stepsService->createWorkflowOfferTargetListMap($this->workflowId);

        // Build out list of email addresses to check
        foreach($records as $record) {
            $emails[] = $record->emailAddress;
        }

        // Run each suppression check
        foreach($this->suppressors as $suppressor) {
            foreach($suppressor->returnSuppressedEmails($emails) as $emailAddress => $offerIds) {
                $suppressed[strtolower($emailAddress)] = true;
                # We'll need to know which specific offers(s) it came from
                # And we need to map these to esp-specific lists
                $espTargetLists = [];

                foreach ($offerIds as $offerId) {
                    if(isset($espTargetAssoc[$offerId])) {
                        $espTargetLists[] = $espTargetAssoc[$offerId];
                    }
                }

                $this->suppStrategy->setTargets($espTargetLists);
                $this->suppStrategy->processSuppression($supp->email_address);
            }
        }

        // Update status
        foreach ($records as $record) {
            if (isset($suppressed[strtolower($record->emailAddress)])) {
                $record->isSuppressed = true;
            }
            else {
                $record->isSuppressed = false;
            }
            $finalRecords[] = $record;
        }

        return $finalRecords;
    }

    public function registerSuppression(IFeedSuppression $service) {
        $this->suppressors[] = $service;
        return $this;
    }

    public function setSuppressionProcessingStrategy(ISuppressionProcessingStrategy $suppStrategy) {
        $this->suppStrategy = $suppStrategy;
    }
}