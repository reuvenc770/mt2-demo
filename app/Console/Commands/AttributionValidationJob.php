<?php

namespace App\Jobs;

use App\Models\JobEntry;
use App\Factories\DataProcessingFactory;

class AttributionValidationJob extends SafeJob {

    private $lookback;
    private $jobName = 'AttributionValidation';
    private $startPoint;

    public function __construct($startPoint, $tracking) {
        $this->startPoint = $startPoint;
        JobTracking::startAggregationJob($this->jobName, $tracking);
    }

    protected function handleJob() {
        $service = DataProcessingFactory::create($this->jobName);
        $service->process($this->startPoint);
    }

}