<?php

namespace App\Jobs;

use App\Factories\FeedProcessingFactory;
use App\DataModels\ProcessingRecord;
use App\DataModels\RecordProcessingReportUpdate;
use Carbon\Carbon;

class ProcessThirdPartyMissedFeedRecordsJob extends MonitoredJob {
    const PARTY = 3;
    private $hoursBack;

    public function __construct($jobName, $hoursBack, $tracking, $runtimeThreshold) {
        $this->hoursBack = $hoursBack;

        parent::__construct($jobName, $runtimeThreshold, $tracking);
    }

    public function handleJob() {
        $recordService = \App::make(\App\Services\RawFeedEmailService::class);
        $records = $recordService->getMissedRecords(self::PARTY, $this->hoursBack);

        if (count($records) > 0) {
            $service = FeedProcessingFactory::createService(self::PARTY);
            $records = $service->suppress($records);
            $service->process($records);
        }

        return count($records);
    }

}