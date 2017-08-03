<?php

namespace App\Jobs;

use App\Factories\FeedProcessingFactory;
use App\Repositories\EtlPickupRepo;

class ProcessFeedRecordsJob extends MonitoredJob {

    private $party;
    private $records;
    private $feedId;
    private $maxId;

    public function __construct($party, $feedId, $records, $etlName, $maxId, $tracking, $runtimeThreshold) {
        $this->jobName = $etlName;
        $this->tracking = $tracking;
        $this->records = $records;
        $this->party = $party;
        $this->feedId = $feedId;
        $this->maxId = $maxId;

        parent::__construct($this->jobName,$runtimeThreshold,$tracking);
    }

    public function handleJob() {
        $pickupRepo = \App::make(\App\Repositories\EtlPickupRepo::class);

        $service = FeedProcessingFactory::createService($this->party, $this->feedId);
        $service->process($this->records);

        $pickupRepo->updateOrCreate($this->jobName, $this->maxId);
        return count($this->records);
    }

}
