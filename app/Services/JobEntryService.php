<?php
/**
 * Created by PhpStorm.
 * User: pcunningham
 * Date: 1/25/16
 * Time: 3:14 PM
 */

namespace App\Services;


use App\Models\JobEntry;
use App\Repositories\JobEntryRepo;
use Carbon\Carbon;

class JobEntryService
{
    protected $repo;
    protected $jobName;


    CONST ROOM = "#mt2-dev-failed-jobs";

    public function __construct( JobEntryRepo $repo)
    {
        $this->repo = $repo;
    }

    public function startEspJob($jobName, $espName, $accountName, $tracking)
    {
        $this->jobName = $jobName;
        $espJob = $this->repo->startEspJobReturnObject($jobName, $espName, $accountName, $tracking);
        $espJob->time_started = Carbon::now();
        $espJob->attempts = 1;
        $espJob->status = JobEntry::RUNNING;
        $espJob->save();

    }

    public function changeJobState($state, $tracking, $tries)
    {
        $job =  $this->repo->getJobByTracking($tracking);
        $job->status = $state;
        $job->attempts = $tries;
        $job->time_finished = Carbon::now();
        $job->save();
    }

    public function startTrackingJob($jobName, $startDate, $endDate, $tracking) 
    {
        $this->jobName = $jobName;
        $trackingJob = $this->repo->startTrackingJobReturnObject($jobName, $startDate, $endDate, $tracking);
        $trackingJob->time_started = Carbon::now();
        $trackingJob->attempts = 1;
        $trackingJob->status = JobEntry::RUNNING;
        $trackingJob->save();
    }

}