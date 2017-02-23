<?php

namespace App\Jobs;

use App\Jobs\SafeJob;
use App\Factories\ServiceFactory;

class DataConsistencyValidationJob extends SafeJob {

    private $source;
    private $type;

    public function __construct($source, $type, $tracking) {
        $this->source = $source;
        $this->type = $type;
        $jobName = 'DataValidation-' . $source;
        parent::__construct($jobName, $tracking);
    }

    protected function handleJob() {
        $service = ServiceFactory::createDataValidationService($this->source, $this->type);
        $service->runComparison($this->type);
    }

}