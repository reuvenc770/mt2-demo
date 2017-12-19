<?php

namespace App\Jobs;
use App\Facades\JobTracking;

class VacuumRedshiftJob extends MonitoredJob  {

    protected $tracking;
    private $repo;
    protected $jobName = 'OptimizeRedshift';

    public function __construct($repo, $tracking, $runtimeThreshold) {
        $this->repo = $repo;
        parent::__construct($this->jobName,$runtimeThreshold,$tracking);
    }

    public function handleJob() {
        $this->repo->optimizeDb();
    }
}