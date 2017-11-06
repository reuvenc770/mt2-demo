<?php

namespace App\Jobs;

use App\Factories\ServiceFactory;

class DataConsistencyValidationJob extends MonitoredJob {

    private $source;
    private $type;

    public function __construct($source, $type, $tracking, $runtimeThreshold) {
        $this->source = $source;
        $this->type = $type;
        $jobName = 'DataValidation-' . $source;
        parent::__construct($jobName, $runtimeThreshold,$tracking);
    }

    protected function handleJob() {
        $service = ServiceFactory::createDataValidationService($this->source, $this->type);
        $count = $service->runComparison($this->type);

        return $count;
    }

}