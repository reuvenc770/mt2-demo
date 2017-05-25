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
use Illuminate\Support\Facades\DB;

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

    /**
     * general purpose job_entries creator/updater
     * @param string $tracking
     * @param array $params
     */
    public function saveJob(string $tracking, array $params){
        $this->entry->updateOrCreate(
            array('tracking' => $tracking),
            $params
        );
    }

    public function updateJobStatuses($daterange){
        return DB::update("UPDATE job_entries
                    SET status=
                    CASE
                        WHEN status IN (1,7) AND time_started < NOW() - INTERVAL IFNULL(runtime_seconds_threshold,3600) SECOND THEN 10
                        WHEN status IN (1,7) AND time_started < NOW() - INTERVAL TRUNCATE(IFNULL(runtime_seconds_threshold,3600)*0.75,0) SECOND THEN 9
                        WHEN status=5 AND time_fired < NOW() - INTERVAL 4 HOUR
                    ELSE status
                    END
                    WHERE status IN(1,5,7) AND time_fired ".$daterange."
                    AND runtime_seconds_threshold !=0
                  ");
    }


    public function generateRunTimeReport($daterange){
        return DB::select("SELECT
                            CASE
                              WHEN status=5 THEN '1 QUEUED'
                              WHEN status=12 THEN '2 LONG_ONQUEUE'
                              WHEN status=4 THEN '3 WAITING'
                              WHEN status=6 THEN '4 SKIPPED'
                              WHEN status=1 THEN '5 RUNNING'
                              WHEN status=7 THEN '6 RUNNING ACCEPTANCE TEST'
                              WHEN status=2 THEN '7 SUCCESS'
                              WHEN status=9 THEN '8 RUNTIME WARNING'
                              WHEN status=10 THEN '9 RUNTIME ERROR'
                              WHEN status=3 THEN '10 FAILED'
                              WHEN status=8 THEN '11 ACCEPTANCE TEST FAILED'
                              WHEN status=11 THEN '12 RESOLVED'
                            END as `status `,
                            COUNT(status) AS count
                            FROM job_entries
                            WHERE time_fired ".$daterange."
                            #AND runtime_seconds_threshold !=0
                            GROUP BY `status `
                            ORDER BY `status `
                          ");
    }

    public function retrieveBadJobs($daterange){
        return DB::select("SELECT
                            CASE
                            WHEN status=9 THEN 'warning'
                            ELSE 'error'
                            END AS type,
                            CASE
                            WHEN status=9 THEN 'RUNTIME WARNING'
                            WHEN status=10 THEN 'RUNTIME ERROR'
                            WHEN status=3 THEN 'FAILED'
                            WHEN status=8 THEN 'ACCEPTANCE TEST FAILED'
                            END as message,
                            id,
                            job_name,
                            runtime_seconds_threshold,
                            time_fired,
                            time_started,
                            time_finished,
                            attempts,
                            status,
                            diagnostics
                            FROM job_entries
                            WHERE time_fired ".$daterange."
                            AND status IN(3,8,9,10)
                            #AND runtime_seconds_threshold !=0
                            ORDER BY id DESC
                          ");
    }

    public function resolveJobs($daterange){
        return DB::update("UPDATE job_entries
                            SET diagnostics = JSON_SET(diagnostics,'$.status_update',CONCAT('status RESOLVED from status=',status)),
                            status=11
                            WHERE status NOT IN(2,11) AND time_fired ".$daterange."
                            AND runtime_seconds_threshold !=0
                          ");
    }

}