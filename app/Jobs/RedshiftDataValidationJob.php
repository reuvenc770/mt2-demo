<?php

namespace App\Jobs;

use App\Jobs\SafeJob;
use App\Factories\ServiceFactory;

class RedshiftDataValidationJob extends SafeJob {

    private $entity;
    private $lookback;

    public function __construct($entity, $lookback, $tracking) {
        $this->entity = $entity;
        $this->lookback = $lookback;
        $jobName = 'DataValidation-' . $source;
        parent::__construct($jobName, $tracking);
    }

    protected function handleJob() {
        $strategy = ServiceFactory::createRedshiftValidator($this->entity);
        $result = $strategy->test();

        if (!$result) {
            $strategy->fix();
        }
    }

}