<?php

namespace App\Jobs;

use App\Factories\DataProcessingFactory;

class AttributionValidationJob extends MonitoredJob {

    protected $jobName = 'AttributionValidation';
    private $startPoint;

    public function __construct($startPoint, $tracking, $runtimeThreshold) {
        $this->startPoint = $startPoint;
        parent::__construct($this->jobName, $runtimeThreshold, $tracking);
    }

    protected function handleJob() {
        $service = DataProcessingFactory::create($this->jobName);
        return $service->process($this->startPoint);
    }

}