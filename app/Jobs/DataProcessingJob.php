<?php

namespace App\Jobs;

use App\Models\JobEntry;
use App\Facades\JobTracking;
use App\Factories\DataProcessingFactory;

class DataProcessingJob extends MonitoredJob {

    private $lookback;

    public function __construct($jobName, $tracking, $lookback = null, $runtime_threshold) {
        $this->lookback = $lookback;

        parent::__construct($jobName, $runtime_threshold, $tracking);
    }

    protected function handleJob() {
        $service = DataProcessingFactory::create($this->jobName);
        $service->extract($this->lookback);
        $service->load();
    }
}