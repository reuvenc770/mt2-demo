<?php

namespace App\Jobs;

use App\Models\JobEntry;
use App\Facades\JobTracking;
use App\Factories\DataProcessingFactory;

class DataProcessingJob extends SafeJob {

    private $lookback;

    public function __construct($jobName, $tracking, $lookback = null) {
        $this->lookback = $lookback;
        JobTracking::startAggregationJob($jobName, $tracking);
    }

    protected function handleJob() {
        $service = DataProcessingFactory::create($this->jobName);
        $service->extract($this->lookback);
        $service->load();
    }

}