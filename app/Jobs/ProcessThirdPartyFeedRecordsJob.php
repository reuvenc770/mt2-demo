<?php

namespace App\Jobs;

use App\Factories\FeedProcessingFactory;

class ProcessThirdPartyFeedRecordsJob extends MonitoredJob {

    const PARTY = 3;
    private $startChars;
    private $startPoint;

    public function __construct($startChars, $jobName, $startPoint, $tracking, $runtimeThreshold) {
        $this->startPoint = $startPoint;
        $this->startChars = $startChars;

        parent::__construct($jobName, $runtimeThreshold, $tracking);
    }

    protected function handleJob() {
        $rawService = \App::make(\App\Services\RawFeedEmailService::class);
        $pickupRepo = \App::make(\App\Repositories\EtlPickupRepo::class);
        $records = $rawService->getThirdPartyRecordsWithChars($this->startPoint, $this->startChars);
        $maxId = $rawService->getMaxIdPulled();

        if ($maxId > $this->startPoint) {
            $service = FeedProcessingFactory::createService(self::PARTY);
            $records = $service->suppress($records);
            $service->process($records);
            $pickupRepo->updateOrCreate($this->jobName, $maxId);
        }
        
        return count($records);
    }

}
