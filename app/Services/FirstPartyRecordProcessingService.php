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
    private $emailRepo;

    private $processingDate;

    public function __construct(AbstractReportService $espApiService) {
        $this->espApiService = $espApiService;
        $this->processingDate = Carbon::today()->format('Y-m-d');
    }

    public function processPartyData(array $records) {
        $count = $this->espApiService->pushRecords($records, $this->targetId);

        /**
            Maybe we need feed id here for reporting purposes?
        */
    }

    public function setTargetId($targetId) {
        $this->targetId = $targetId;
    }

}