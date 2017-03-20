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
use App\Exceptions\JobCompletedException;
use Log;
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

        // start this job only if it hasn't been finished before
        if (null === $espJob->time_finished || '0000-00-00 00:00:00' === $espJob->time_finished) {
            $espJob->time_fired = Carbon::now();
            $espJob->attempts = 0;
            $espJob->campaign_id = $campaignId;
            $espJob->status = JobEntry::ONQUEUE;
            $espJob->save();
        }
        else {
            $espJob->status = JobEntry::SUCCESS;
            $espJob->save();
            throw new JobCompletedException("Job $jobName already completed");
        }

    }

    public function changeJobState($state, $tracking, $total = 0)
    {
        $job = $this->repo->getJobByTracking($tracking);
        $job->status = $state;
        if($state == JobEntry::SUCCESS) {
            $job->rows_impacted = $total;
            $job->time_finished = Carbon::now();
            $job->save();
        }
        else if ((null !== $job->time_finished) && ('0000-00-00 00:00:00' !== $job->time_finished) && ($job->time_finished >= Carbon::now()->subHour(12))) {
            $job->status = JobEntry::SUCCESS;
            $job->save();
            throw new JobCompletedException("Job {$job->job_name}, {$tracking} already completed at {$job->time_finished}. Attempted rerun at " . Carbon::now());
        }
        else if($state == JobEntry::RUNNING){
            $job->time_started = Carbon::now();
            $job->attempts = $job->attempts + 1;
            $job->save();
        } else if ($state == JobEntry::SKIPPED){
                $job->save();
        }

        if($state == JobEntry::FAILED){
            $job->save();
            Slack::to(self::ROOM)->send("{$job->job_name} for {$job->account_name} - {$job->account_number} has failed after running {$job->attempts} attempts");
        }
    }

    public function startTrackingJob($jobName, $startDate, $endDate, $tracking)
    {
        $this->jobName = $jobName;
        $trackingJob = $this->repo->startTrackingJobReturnObject($jobName, $startDate, $endDate, $tracking);
        $trackingJob->time_fired = Carbon::now();
        $trackingJob->attempts = 0;
        $trackingJob->status = JobEntry::ONQUEUE;
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

        $espJob->time_fired = Carbon::now();
        $espJob->attempts = 0;
        $espJob->status = JobEntry::ONQUEUE;
        $espJob->save();
    }

    public function isRerunJobAlreadyQueued($name, $campaignId) {
        return $this->repo->isRerunJobAlreadyQueued($name, $campaignId);
    }

}