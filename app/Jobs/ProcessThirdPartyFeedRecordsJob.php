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
        $repo = \App::make(\App\Repositories\RawFeedEmailRepo::class);
        $pickupRepo = \App::make(\App\Repositories\EtlPickupRepo::class);
        $records = $repo->getThirdPartyRecordsWithChars($this->startPoint, $this->startChars);

        if (count($records) > 0) {
            $service = FeedProcessingFactory::createService(self::PARTY);
            $records = $service->suppress($records);
            $service->process($records);

            $maxId = $service->getLastProcessedId();
            $pickupRepo->updateOrCreate($this->jobName, $maxId);
        }
        
        return count($records);
    }

}
