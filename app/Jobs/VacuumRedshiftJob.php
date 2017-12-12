<?php

namespace App\Jobs;
use App\Facades\JobTracking;

/**
 * Optimizes tables in Redshift
 *
 * @package ListProfile
 * @uses App.Repositories.ListProfileFlatTableRepo.html App\Repositories\ListProfileFlatTableRepo
 */
class VacuumRedshiftJob extends MonitoredJob  {
    /**
     * Unique job tracking ID. 
     *
     * @var string
     */
    protected $tracking;

    /**
     * Name of this job.
     *
     * @var string
     */
    protected $jobName = 'OptimizeRedshift';

    /**
     * List Profile Flat Table Repository
     *
     * @var App\Repositories\ListProfileFlatTableRepo
     */
    private $repo;

    /**
     * Create a new VacuumRedshiftJob instance
     *
     * @param \App\Repositories\ListProfileFlatTableRepo $repo
     * @param string $tracking
     * @param string $runtimeThreshold
     * @return void
     */
    public function __construct($repo, $tracking, $runtimeThreshold) {
        $this->repo = $repo;
        parent::__construct($this->jobName,$runtimeThreshold,$tracking);
    }

    /**
     * Optimizes Redshift
     *
     * @return void
     */
    public function handleJob() {
                $this->repo->optimizeDb();
    }
}
