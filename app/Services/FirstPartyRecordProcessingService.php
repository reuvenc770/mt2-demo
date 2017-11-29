<?php

namespace App\Services;
use App\Repositories\FirstPartyRecordDataRepo;
use App\Repositories\FeedDateEmailBreakdownRepo;
use App\Repositories\EspWorkflowLogRepo;
use App\Services\Interfaces\IFeedPartyProcessing;
use App\Services\Interfaces\IPostingStrategy;
use App\Services\AbstractReportService;
use Carbon\Carbon;
use App\DataModels\RecordProcessingReportUpdate;

class FirstPartyRecordProcessingService implements IFeedPartyProcessing {
    private $espApiService;
    private $emailRepo;
    private $statsRepo;
    private $recordDataRepo;
    private $workflowLogRepo;

    private $emailCache = [];
    private $targetId;
    private $feedId;
    private $workflowId;
    private $processingDate;

    public function __construct(AbstractReportService $espApiService, 
        FeedDateEmailBreakdownRepo $statsRepo, 
        FirstPartyRecordDataRepo $recordDataRepo,
        IPostingStrategy $postingStrategy,
        EspWorkflowLogRepo $workflowLogRepo) {

        $this->espApiService = $espApiService;
        $this->statsRepo = $statsRepo;
        $this->recordDataRepo = $recordDataRepo;
        $this->postingStrategy = $postingStrategy;
        $this->processingDate = Carbon::today()->format('Y-m-d');
        $this->workflowLogRepo = $workflowLogRepo;
    }

    public function processPartyData(array $records, RecordProcessingReportUpdate $reportUpdate) {
        $postingRecords = $this->postingStrategy->prepareForPosting($records, $this->targetId);

        foreach($postingRecords as $record) {
            $result = $this->espApiService->addContactToLists($record->email_address, [$this->targetId]);

            $this->workflowLogRepo->insert([
                'workflow_id' => $this->workflowId,
                'email_id' => $record->emailId,
                'target_list' => $this->targetId,
                'status_received' => $result
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

}