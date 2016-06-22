<?php
/**
 * Created by PhpStorm.
 * User: pcunningham
 * Date: 1/25/16
 * Time: 3:12 PM
 */

namespace App\Repositories;


use App\Models\JobEntry;

use Illuminate\Support\Facades\Log;
class JobEntryRepo
{
    /**
     * @var JobEntry
     */
    protected $entry;

    /**
     * JobEntryRepo constructor.
     * @param JobEntry $entry
     */
    public function __construct(JobEntry $entry)
    {
        $this->entry = $entry;
    }

    /**
     * @param $jobName
     * @param $espName
     * @param $accountName
     * @return JobEntry
     */
    public function startEspJobReturnObject($jobName, $espName, $accountName, $tracking){
        return $this->entry->updateOrCreate(array('tracking' => $tracking),['job_name' => $jobName,
                                     'account_name'=> $espName,
                                     'account_number' => $accountName,
                                     'tracking' => $tracking]);
    }

    public function startAggregateJobReturnObject($jobName, $tracking){
        return $this->entry->updateOrCreate(array('tracking' => $tracking),['job_name' => $jobName,
                                     'tracking' => $tracking]);
    }

    public function startTrackingJobReturnObject($jobName, $startDate, $endDate, $tracking) {
        return $this->entry->updateOrCreate(array('tracking' => $tracking),[
                'job_name' => $jobName . $startDate . '::' . $endDate,
                'account_name' => 'cake',
                'tracking' => $tracking
            ]);
    }

    public function getJobByTracking($tracking){
        try{
            return $this->entry->where('tracking',$tracking)->firstOrFail();
        } catch(\Exception $e){
            Log::error($e->getMessage());
        }

    }

    public function getLastJobs($numberOfRecords){
        return $this->entry->orderBy('id', 'desc')
            ->take($numberOfRecords)
            ->get();
    }

    public function alreadyRunning($jobName) {
        return $this->entry
            ->where('job_name', $jobName)
            ->whereIn('status', [JobEntry::RUNNING, JobEntry::WAITING])
            ->count() > 0;
    }

    public function isComplete($tracking) {
        // A job is complete if time_finished is not null
        // Get entry (by tracking) where the time_finished is not null
        // If such a row exists, it is complete.
        
        $result = $this->entry
            ->where('tracking', $tracking)
            ->whereNotNull('time_finished');

        return !$result->isEmpty();
    }

    public function isRerunJobAlreadyQueued($name, $campaignId) {
        $count = $this->entry
                    ->where('job_name', $name)
                    ->where('campaign_id', $campaignId)
                    ->whereIn('status', [1,4,5])
                    ->count();
        return $count > 0;
    }

}