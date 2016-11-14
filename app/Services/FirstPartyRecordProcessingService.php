<?php

namespace App\Services;
use App\DataModels\ProcessingRecord;
use App\Repositories\EmailRepo;
use App\Repositories\AttributionLevelRepo;
use App\Repositories\RecordDataRepo;
use App\Repositories\FeedDateEmailBreakdownRepo;
use App\Services\Interfaces\IFeedPartyProcessing;
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

    public function __construct(AbstractReportService $espApiService, FeedDateEmailBreakdownRepo $statsRepo, FirstPartyRecordDataRepo $recordDataRepo) {
        $this->espApiService = $espApiService;
        $this->statsRepo = $statsRepo;
        $this->recordDataRepo = $recordDataRepo;
        $this->processingDate = Carbon::today()->format('Y-m-d');
    }

    public function processPartyData(array $records) {
        $count = $this->espApiService->pushRecords($records, $this->targetId);

        // We need to add them to first_party_record_data
        // Then add functionality to pull from this table for list profiles if the feed selected is 1st party
        // Then we need to ... (got interrupted)
        $uniqueCount = 0;
        $duplicateCount = 0;
        foreach($records as $record) {
            if ($this->recordDataRepo->isUnique($record->emailId, $this->feedId)) {
                $this->recordDataRepo->insert($record->mapToRecordData());
                $uniqueCount++;
            }
            else {
                $duplicateCount++;
            }
        }

        $this->recordDataRepo->insertStored();
        $this->statsRepo->massUpdateStatuses([$this->feedId => ['unique' => $uniqueCount, 'duplicate' => $duplicateCount]]);

    }

    public function setTargetId($targetId) {
        $this->targetId = $targetId;
    }

    public function setFeedId($feedId) {
        $this->feedId = $feedId;
    }

}