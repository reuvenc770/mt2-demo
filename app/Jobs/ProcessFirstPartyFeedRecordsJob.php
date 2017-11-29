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
        $repo = \App::make(\App\Repositories\RawFeedEmailRepo::class);
        $pickupRepo = \App::make(\App\Repositories\EtlPickupRepo::class);
        $records = $repo->getFirstPartyRecordsFromFeed($this->startPoint, $this->feedId);

        if (count($records) > 0) {
            $service = FeedProcessingFactory::createService(self::PARTY, $this->feedId);
            $records = $service->suppress($records);
            $service->process($records);

            $maxId = $service->getLastProcessedId();
            $pickupRepo->updateOrCreate($this->jobName, $maxId);
        }
        
        return count($records);
    }

}