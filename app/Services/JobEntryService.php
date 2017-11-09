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
use Mockery\CountValidator\Exception;

class JobEntryService
{
    protected $repo;
    protected $room;
    CONST ROOM = '#cmp_hard_start_errors';

    public function __construct(JobEntryRepo $repo)
    {
        $this->repo = $repo;
        $this->room = env('SLACK_CHANNEL',self::ROOM);
    }

    public function startEspJob($jobName, $espName, $accountName, $tracking, $campaignId = 0)
    {
        $espJob = $this->repo->getJobByTracking($tracking);

        // start this job only if it hasn't been finished before
        if ($espJob && (null === $espJob->time_finished || '0000-00-00 00:00:00' === $espJob->time_finished)) {
            $espJob->status = JobEntry::SUCCESS;
            $espJob->save();
            throw new JobCompletedException("Job $jobName already completed");
        }
        else {
            $espJob = $this->repo->startEspJobReturnObject($jobName, $espName, $accountName, $tracking);
            $espJob->attempts = 0;
            $espJob->campaign_id = $campaignId;
            $espJob->status = JobEntry::ONQUEUE;
            $espJob->save();
        }
    }

    public function changeJobState($state, $tracking, $total = 0)
    {
        $job = $this->repo->getJobByTracking($tracking);
        
        if($state == JobEntry::SUCCESS) {
            $job->status = $state;
            $job->rows_impacted = $total;
            $job->time_finished = Carbon::now();
            $job->save();
        }
        else if (JobEntry::FAILED !== $job->status && (null !== $job->time_finished) && ('0000-00-00 00:00:00' !== $job->time_finished) && ($job->time_finished >= Carbon::now()->subHour(12))) {
            throw new JobCompletedException("Job {$job->job_name}, {$tracking} already completed at {$job->time_finished} with status {$job->status}. Attempted rerun at " . Carbon::now());
        }
        else if($state == JobEntry::RUNNING){
            $job->status = $state;
            $job->time_started = Carbon::now();
            $job->attempts = $job->attempts + 1;
            $job->save();
        }
        else if($state == JobEntry::SKIPPED || $state == JobEntry::RUNNING_ACCEPTANCE_TEST){
            $job->status = $state;
            $job->save();
        }
        else if($state == JobEntry::FAILED){
            $job->status = $state;
            $job->time_finished = Carbon::now()->toDateTimeString();
            $job->save();
            Slack::to($this->room)->send("{$job->job_name} for {$job->account_name} - {$job->account_number} has failed after running {$job->attempts} attempts (job_entries.id=$job->id)");
        } 
        else if($state == JobEntry::ACCEPTANCE_TEST_FAILED){
            $job->status = $state;
            $job->save();
            Slack::to($this->room)->send("{$job->job_name} for {$job->account_name} - {$job->account_number} has failed acceptance test (job_entries.id=$job->id)");
        }
        else {
            Slack::to($this->room)->send("Attempting to set status $state for {$job->job_name} (job_entries.id=$job->id)");
        }
    }

    public function getJobState($tracking){
        return $this->repo->getJobByTracking($tracking)->status;
    }

    public function startTrackingJob($jobName, $startDate, $endDate, $tracking)
    {
        $this->repo->startTrackingJobReturnObject($jobName, $startDate, $endDate, $tracking);
    }

    public function getTrailingLogList()
    {
        return $this->repo->getLastJobs(50);
    }

    public function startAggregationJob($jobName, $tracking)
    {
        $this->repo->startAggregateJobReturnObject($jobName, $tracking);
    }

    public function isRerunJobAlreadyQueued($name, $campaignId) {
        return $this->repo->isRerunJobAlreadyQueued($name, $campaignId);
    }

    public function getJobProfile($tracking){
        return $this->repo->getJobByTracking($tracking);
    }

    public function addDiagnostic($diagnostic,$tracking){
        if(is_object($diagnostic)){
            $diagnostic = (array) $diagnostic;
        }
        $job = $this->repo->getJobByTracking($tracking);
        $current_diagnostics = $job->diagnostics!=null ? $job->diagnostics : '{}';
        $job->diagnostics = json_encode(array_merge_recursive(json_decode($current_diagnostics,TRUE),$diagnostic),JSON_PRETTY_PRINT);
        $job->save();
    }

    /**
     * expects job_name and runtime_seconds_threshold in params
     * @param $tracking
     * @param $params
     */
    public function initiateNewMonitoredJob($tracking,$params){
        if(!isset($params['job_name']) || !isset($params['runtime_seconds_threshold'])){
            Log::critical('missing required parameters');
            return;
        }
        $params['time_fired'] = Carbon::now();
        $params['attempts'] = 0;
        $params['status'] = JobEntry::ONQUEUE;
        if(isset($params['diagnostics'])){
            $params['diagnostics'] = json_encode($params['diagnostics']);
        }
        $this->saveJob($tracking,$params);
    }

    /**
     * general purpose create/update job
     * @param $tracking
     * @param $params
     */
    public function saveJob($tracking,$params){
        $this->repo->saveJob($tracking,$params);
    }

    public function updateJobStatuses($daterange){
        return $this->repo->updateJobStatuses($daterange);
    }

    public function generateRunTimeReport($daterange){
        return $this->repo->generateRunTimeReport($daterange);
    }

    public function retrieveBadJobs($daterange){
        return $this->repo->retrieveBadJobs($daterange);
    }

    public function retrieveBadJobsConsolidated($daterange){
        return $this->repo->retrieveBadJobsConsolidated($daterange);
    }

    public function resolveJobs($daterange){
        return $this->repo->resolveJobs($daterange);
    }
}
