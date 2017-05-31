<?php

namespace App\Jobs;

use App\Jobs\MonitoredJob;
use App\Factories\ServiceFactory;
use Log;

class RedshiftDataValidationJob extends MonitoredJob {

    private $entity;
    private $lookback;

    public function __construct($entity, $lookback, $tracking, $runtimeThreshold) {
        $this->entity = $entity;
        $this->lookback = $lookback;
        $jobName = 'DataValidation-' . $entity;
        parent::__construct($jobName, $runtimeThreshold, $tracking);
    }

    protected function handleJob() {
        $strategy = ServiceFactory::createRedshiftValidator($this->entity);
        $result = $strategy->test($this->lookback);

        if (!$result) {
            Log::error($this->entity . ' failed test.');
            $strategy->fix();
        }
    }

}