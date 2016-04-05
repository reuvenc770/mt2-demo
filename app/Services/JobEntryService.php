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
use Maknz\Slack\Facades\Slack;
class JobEntryService
{
    protected $repo;
    protected $jobName;


    CONST ROOM = "#mt2-dev-failed-jobs";

    public function __construct(JobEntryRepo $repo)
    {
        $this->repo = $repo;
    }

    public function startEspJob($jobName, $espName, $accountName, $tracking, $campaignId = 0)
    {
        $this->jobName = $jobName;
        $espJob = $this->repo->startEspJobReturnObject($jobName, $espName, $accountName, $tracking);
        $espJob->time_started = Carbon::now();
        $espJob->attempts = 1;
        $espJob->campaign_id = $campaignId;
        $espJob->status = JobEntry::RUNNING;
        $espJob->save();

    }

    public function changeJobState($state, $tracking, $tries)
    {
        $job = $this->repo->getJobByTracking($tracking);
        $job->status = $state;
        $job->attempts = $tries;
        if($state == JobEntry::SUCCESS) {
            $job->time_finished = Carbon::now();
        }
        $job->save();
        if($job->status == 3 && env("SLACK_ON",false)){
          Slack::to('#mt2-dev-failed-jobs')->send("{$job->job_name} for {$job->account_name} - {$job->account_number} has failed after running {$job->attempts} attempts");
        }
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

    public function getTrailingLogList()
    {
        return $this->repo->getLastJobs(50);
    }

    public function startAggregationJob($jobName, $tracking)
    {
        $this->jobName = $jobName;
        $espJob = $this->repo->startAggregateJobReturnObject($jobName, $tracking);
        $espJob->time_started = Carbon::now();
        $espJob->attempts = 1;
        $espJob->status = JobEntry::RUNNING;
        $espJob->save();
    }

}