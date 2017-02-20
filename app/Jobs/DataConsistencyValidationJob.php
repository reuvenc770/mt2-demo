<?php

namespace App\Jobs;

use App\Factories\ServiceFactory;

class DataConsistencyValidationJob extends SafeJob {

    private $source;
    private $type;
    private $field;

    public function __construct($source, $type, $field, $tracking) {
        $this->source = $source;
        $this->type = $type;
        $this->field = $field;
        $jobName = 'DataValidation-' . $source;
        parent::__construct($jobName, $tracking);
    }

    public function handleJob() {
        $service = ServiceFactory::createDataValidationService($this->source, $this->type, $this->field);
        $service->runComparison($this->type, $this->fieldName);
    }

}