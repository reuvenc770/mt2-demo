<?php

namespace App\Services;
use App\Repositories\FirstPartyRecordDataRepo;
use App\Repositories\FeedDateEmailBreakdownRepo;
use App\Services\Interfaces\IFeedPartyProcessing;
use App\Services\Interfaces\IPostingStrategy;
use App\Services\AbstractReportService;
use Carbon\Carbon;

class FirstPartyRecordProcessingService implements IFeedPartyProcessing {
    private $emailCache = [];
    private $espApiService;
    private $targetId;
    private $feedId;
    private $emailRepo;
    private $statsRepo;
    private $recordDataRepo;

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
        $postingRecords = $this->postingStrategy->prepareForPosting($records);
        $count = $this->espApiService->pushRecords($postingStrategy, $this->targetId);
        $this->updateStats($records);
    }

    public function setTargetId($targetId) {
        $this->targetId = $targetId;
    }

    public function setFeedId($feedId) {
        $this->feedId = $feedId;
    }

    private function updateStats($records) {
        // We need to add them to first_party_record_data
        $uniqueCount = 0;
        $duplicateCount = 0;

        $statuses = [];

        foreach($records as $record) {
            $domainGroupId = $record->domainGroupId;
            $this->recordDataRepo->insert($record->mapToRecordData());

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

            if ($this->recordDataRepo->isUnique($record->emailId, $this->feedId)) {
                $statuses[$record->feedId][$domainGroupId]['unique']++;
            }
            else {
                $statuses[$record->feedId][$domainGroupId]['duplicate']++;
            }
        }

        $this->recordDataRepo->insertStored();
        $this->statsRepo->massUpdateValidEmailStatus($statuses);
    }

}