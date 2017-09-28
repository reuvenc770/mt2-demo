<?php

namespace App\Jobs;

use App\Factories\FeedProcessingFactory;
use App\DataModels\ProcessingRecord;
use App\DataModels\RecordProcessingReportUpdate;
use Carbon\Carbon;

class ProcessFirstPartyMissedFeedRecordsJob extends MonitoredJob {
    const PARTY = 1;
    private $feedid;
    private $hoursBack;

    public function __construct($jobName, $feedId, $hoursBack, $tracking, $runtimeThreshold) {
        $this->party = $party;
        $this->feedId = $feedId;
        $this->hoursBack = $hoursBack;

        parent::__construct($jobName, $runtimeThreshold, $tracking);
    }

    public function handleJob() {
        /*
            Stub. Not fully implemented yet.
        
        $recordService = \App::make(\App\Services\RawFeedEmailService::class);
        $records = $recordService->getMissedRecords(self::PARTY, $this->hoursBack);

        if (count($records) > 0) {
            $service = FeedProcessingFactory::createService(self::PARTY, $this->feedId);
            $records = $service->suppress($records);
            $service->process($records);
        }

        return count($records);
        */
    }

}