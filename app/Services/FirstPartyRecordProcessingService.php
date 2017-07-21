<?php

namespace App\Services;
use App\Repositories\FirstPartyRecordDataRepo;
use App\Repositories\FeedDateEmailBreakdownRepo;
use App\Services\Interfaces\IFeedPartyProcessing;
use App\Services\Interfaces\IPostingStrategy;
use App\Services\AbstractReportService;
use Carbon\Carbon;

class FirstPartyRecordProcessingService implements IFeedPartyProcessing {
    private $espApiService;
    private $emailRepo;
    private $statsRepo;
    private $recordDataRepo;

    private $emailCache = [];
    private $targetId;
    private $feedId;
    private $workflowId;
    private $processingDate;

    public function __construct(AbstractReportService $espApiService, 
        FeedDateEmailBreakdownRepo $statsRepo, 
        FirstPartyRecordDataRepo $recordDataRepo,
        IPostingStrategy $postingStrategy) {

        $this->espApiService = $espApiService;
        $this->statsRepo = $statsRepo;
        $this->recordDataRepo = $recordDataRepo;
        $this->postingStrategy = $postingStrategy;
        $this->processingDate = Carbon::today()->format('Y-m-d');
    }

    public function processPartyData(array $records) {
        $postingRecords = $this->postingStrategy->prepareForPosting($records, $this->targetId);

        foreach($postingRecords as $record) {
            $result = $this->espApiService->addContactToLists($record->email_address, [$this->targetId]);

            $this->workflowLogRepo->insert([
                'workflow_id' => $this->workflowId,
                'email_id' => $record->emailId,
                'target_list' => $this->targetId,
                'status_received' => $result
            ]);
        }

        $this->updateStats($records);
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

    private function updateStats($records) {
        // We need to add them to first_party_record_data
        $uniqueCount = 0;
        $duplicateCount = 0;

        $statuses = [];

        foreach($records as $record) {
            $domainGroupId = $record->domainGroupId;
            $this->recordDataRepo->insert($record->mapToRecordData());
            $filename = $record->file;

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
                    'duplicate' => 0
                ];
            }

            if ($this->recordDataRepo->isUnique($record->emailId, $this->feedId)) {
                $statuses[$record->feedId][$domainGroupId][$filename]['unique']++;
            }
            else {
                $statuses[$record->feedId][$domainGroupId][$filename]['duplicate']++;
            }
        }

        $this->recordDataRepo->insertStored();
        $this->statsRepo->massUpdateValidEmailStatus($statuses);
    }

}