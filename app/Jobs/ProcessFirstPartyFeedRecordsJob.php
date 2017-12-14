<?php

namespace App\Jobs;

use App\Factories\FeedProcessingFactory;

class ProcessFirstPartyFeedRecordsJob extends MonitoredJob {

    const PARTY = 1;
    private $feedId;
    private $startPoint;

    public function __construct($feedId, $jobName, $startPoint, $tracking, $runtimeThreshold) {
        $this->feedId = $feedId;
        $this->startPoint = $startPoint;

        parent::__construct($jobName,$runtimeThreshold,$tracking);
    }

    protected function handleJob() {
        $rawService = \App::make(\App\Services\RawFeedEmailService::class);
        $pickupRepo = \App::make(\App\Repositories\EtlPickupRepo::class);
        $records = $rawService->getFirstPartyRecordsFromFeed($this->startPoint, $this->feedId);
        $maxId = $rawService->getMaxIdPulled();

        if (count($records) > 0) {
            $service = FeedProcessingFactory::createService(self::PARTY, $this->feedId);
            $records = $service->suppress($records);
            $service->process($records);
            $pickupRepo->updateOrCreate($this->jobName, $maxId);
        }
        
        return count($records);
    }

}