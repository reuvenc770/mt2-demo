<?php

namespace App\Jobs;

use App\Factories\FeedProcessingFactory;
use App\DataModels\ProcessingRecord;
use App\DataModels\RecordProcessingReportUpdate;
use Carbon\Carbon;

class ProcessThirdPartyMissedFeedRecordsJob extends MonitoredJob {
    const PARTY = 3;
    const JOB_LIMIT = 1500;
    private $hoursBack;

    public function __construct($jobName, $hoursBack, $tracking, $runtimeThreshold) {
        $this->hoursBack = $hoursBack;

        parent::__construct($jobName, $runtimeThreshold, $tracking);
    }

    public function handleJob() {
        $recordService = \App::make(\App\Services\RawFeedEmailService::class);

        $date = Carbon::today()->toDateString();
        $start = Carbon::now()->subHours($this->hoursBack)->toDateTimeString();
        $minId = $recordService->getMinRawIdForDateTime($start);
        $minInvId = $recordService->getMinInvalidIdForDate($date);

        $totalCount = 0;

        echo "MIN ID: $minId" . PHP_EOL;
        echo "INV ID: $minInvId" . PHP_EOL;

        while ($records = $recordService->getMissedRecords(self::PARTY, $date, $minId, $minInvId, self::JOB_LIMIT)) {
            $count = count($records);
            $totalCount += $count;

            if ($count > 0) {
                $service = FeedProcessingFactory::createService(self::PARTY);
                $records = $service->suppress($records);
                $service->process($records);
                $minId = $recordService->getMaxIdPulled();
            }
            else {
                break;
            }
        }

        return $totalCount;
    }

}