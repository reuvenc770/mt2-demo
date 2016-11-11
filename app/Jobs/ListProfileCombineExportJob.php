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
    private $listProfileCombineId;
    private $jobName;
    private $offerId;
    private $alreadyRan;

    public function __construct($listProfileCombineId, $tracking, $offerId = array(), $alreadyBuiltProfiles = array()) {
        $this->listProfileCombineId = $listProfileCombineId;
        $this->tracking = $tracking;
        $this->jobName = 'ListProfileCombineOnDemand-' . $listProfileCombineId;
        $this->offerId = $offerId;
        $this->alreadyRan = $alreadyBuiltProfiles;
        JobTracking::startAggregationJob($this->jobName, $this->tracking);
    }

    public function handle(ListProfileService $service, ListProfileScheduleService $schedule, ListProfileCombineService $combineService) {
        if ($this->jobCanRun($this->jobName)) {
            try {
                $this->createLock($this->jobName);
                JobTracking::changeJobState(JobEntry::RUNNING, $this->tracking);
                $listProfileCombine = $combineService->getCombineById($this->listProfileCombineId);
                foreach($listProfileCombine->listProfiles as $listProfile) {
                    //check to see if the list profile was already run, if not run it and add it to the list
                    if (!in_array($listProfile->id,$this->alreadyRan)) {
                        $service->buildProfileTable($listProfile->id);
                        $schedule->updateSuccess($listProfile->id);
                        $this->alreadyRan[] = $listProfile->id;
                    }

                }
                JobTracking::changeJobState(JobEntry::SUCCESS, $this->tracking);
                $this->dispatch(new ExportListProfileCombineJob($this->listProfileCombineId,$this->offerId,str_random(16)));
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
        $this->unlock($this->jobName);
    }

}