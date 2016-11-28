<?php

namespace App\Jobs;

use App\Models\JobEntry;
use App\Services\ListProfileCombineService;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Facades\JobTracking;
use App\Jobs\Traits\PreventJobOverlapping;
use App\Services\ListProfileService;
use App\Services\ListProfileScheduleService;
use App\Events\ListProfileCompleted;

class ListProfileCombineExportJob extends Job implements ShouldQueue {
    use InteractsWithQueue, SerializesModels, PreventJobOverlapping, DispatchesJobs;

    private $tracking;
    private $combineId;
    private $jobName;


    public function __construct($combineId, $tracking) {
        $this->combineId = $combineId;
        $this->tracking = $tracking;
        $this->jobName = 'ListProfileCombineExport-' . $combineId;
        JobTracking::startAggregationJob($this->jobName, $this->tracking);
    }

    /**
     * Build a table for each list profile against each offer it used in.
     * 0 for offer is passed for scheduled list exports
     * @param ListProfileService $service
     * @param ListProfileScheduleService $schedule
     */
    public function handle(ListProfileService $service, ListProfileScheduleService $schedule, ListProfileCombineService $combineService) {
        if ($this->jobCanRun($this->jobName)) {
            try {
                $this->createLock($this->jobName);
                JobTracking::changeJobState(JobEntry::RUNNING, $this->tracking);

                $combine = $combineService->getCombineById($this->combineId);
                foreach($combine->listProfiles as $listProfile) {
                    $service->buildProfileTable($listProfile->id);
                    $schedule->updateSuccess($listProfile->id);
                }

                JobTracking::changeJobState(JobEntry::SUCCESS, $this->tracking);

                $this->dispatch(new ExportListProfileCombineJob($this->combineId, str_random(16)));

            }
            catch (\Exception $e) {
                echo "{$this->jobName} failed with {$e->getMessage()}" . PHP_EOL;
                $this->failed();
            }
            finally {
                $this->unlock($this->jobName);
            }
        }
        else {
            echo "Still running {$this->jobName} - job level" . PHP_EOL;
            JobTracking::changeJobState(JobEntry::SKIPPED, $this->tracking);
        }
    }

    public function failed() {
        JobTracking::changeJobState(JobEntry::FAILED, $this->tracking);
    }

}