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

        $statuses = [];

        foreach($records as $record) {
            $domainGroupId = $record->domainGroupId;

            // Note structure
            if (!isset($statuses[$record->feedId])) {
                $statuses[$record->feedId] = [];
                $statuses[$record->feedId][$domainId] = [
                    'unique' => 0,
                    'non-unique' => 0,
                    'duplicate' => 0
                ];
            }
            elseif (!isset($statuses[$record->feedId][$domainId])) {
                $statuses[$record->feedId][$domainId] = [
                    'unique' => 0,
                    'non-unique' => 0,
                    'duplicate' => 0
                ];
            }

            if ($this->recordDataRepo->isUnique($record->emailId, $this->feedId)) {
                $this->recordDataRepo->insert($record->mapToRecordData());
                 $statuses[$record->feedId][$domainId]['unique']++;
            }
            else {
                $statuses[$record->feedId][$domainId]['duplicate']++;
            }
        }

        $this->recordDataRepo->insertStored();
        $this->statsRepo->massUpdateValidEmailStatus($statuses);

    }

    public function setTargetId($targetId) {
        $this->targetId = $targetId;
    }

    public function setFeedId($feedId) {
        $this->feedId = $feedId;
    }

}