<?php

namespace App\Jobs;
use App\Facades\JobTracking;

class VacuumRedshiftJob extends MonitoredJob {

    protected $tracking;
    private $repo;
    protected $jobName = 'OptimizeRedshift';

    public function __construct($repo, $tracking, $runtimeThreshold) {
        $this->repo = $repo;
        $this->tracking = $tracking;

        parent::__construct($this->jobName,$runtimeThreshold,$tracking);

        JobTracking::startAggregationJob($this->jobName, $this->tracking);
    }

    public function handleJob() {
        $this->repo->optimizeDb();
    }

}