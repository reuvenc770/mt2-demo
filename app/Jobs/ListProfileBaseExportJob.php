<?php

namespace App\Jobs;

use App\Models\JobEntry;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Facades\JobTracking;
use App\Jobs\Traits\PreventJobOverlapping;
use App\Services\ListProfileService;
use App\Services\ListProfileScheduleService;
use App\Events\ListProfileCompleted;

class ListProfileBaseExportJob extends Job implements ShouldQueue {
    use InteractsWithQueue, SerializesModels, PreventJobOverlapping, DispatchesJobs;

    private $tracking;
    private $profileId;
    private $jobName;
    private $immediate;

    public function __construct($profileId, $tracking, $immediate = false) {
        $this->profileId = $profileId;
        $this->tracking = $tracking;
        $this->jobName = 'ListProfileExport-' . $profileId;
        JobTracking::startAggregationJob($this->jobName, $this->tracking);
    }

    public function handle(ListProfileService $service, ListProfileScheduleService $schedule) {
        if ($this->jobCanRun($this->jobName)) {
            try {
                $this->createLock($this->jobName);
                JobTracking::changeJobState(JobEntry::RUNNING, $this->tracking);
                $service->buildProfileTable($this->profileId);
                $schedule->updateSuccess($this->profileId);
                JobTracking::changeJobState(JobEntry::SUCCESS, $this->tracking);

                if($this->immediate){ //soon as job is done lets throw it on the FTP
                    $this->dispatch(new ExportListProfileJob($this->profileId,array(),str_random(16)));
                }
                \Event::fire(new ListProfileCompleted($this->profileId));
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