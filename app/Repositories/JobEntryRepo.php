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

}