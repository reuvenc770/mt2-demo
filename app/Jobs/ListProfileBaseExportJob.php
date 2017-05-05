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
use App\Repositories\DeployRepo;
use App\Jobs\ExportDeployCombineJob;
use Carbon\Carbon;
use App\DataModels\CacheReportCard;

class ListProfileBaseExportJob extends Job implements ShouldQueue {
    use InteractsWithQueue, SerializesModels, PreventJobOverlapping, DispatchesJobs;

    private $tracking;
    private $profileId;
    private $jobName;
    private $cacheTagName;

    public function __construct($profileId, $cacheTagName, $tracking) {
        $this->profileId = $profileId;
        $this->tracking = $tracking;
        $this->jobName = 'ListProfileGeneration-' . $profileId;
        $this->cacheTagName = $cacheTagName;
        JobTracking::startAggregationJob($this->jobName, $this->tracking);
    }

    /**
     * Build a table for each list profile against each offer it used in.
     * 0 for offer is passed for scheduled list exports
     * @param ListProfileService $service
     * @param ListProfileScheduleService $schedule
     */
    public function handle(ListProfileService $service, ListProfileScheduleService $schedule, DeployRepo $deployRepo) {
        if ($this->jobCanRun($this->jobName)) {
            try {
                $this->createLock($this->jobName);
                JobTracking::changeJobState(JobEntry::RUNNING, $this->tracking);

                $service->buildProfileTable($this->profileId);
                $this->dispatch(new ExportListProfileJob($this->profileId, str_random(16)));
                $schedule->updateSuccess($this->profileId); // These might not just be scheduled ... 

                if (null !== $this->cacheTagName) {
                    // make this optional
                    Cache::decrement($this->cacheTagName, 1);

                    if ((int)Cache::get($this->cacheTagName) <= 0) {
                        $date = Carbon::today()->toDateString();
                        $deploys = $deployRepo->getDeploysForToday($date);

                        foreach($deploys as $deploy) {
                            $runId = str_random(10);
                            $reportCard = CacheReportCard::makeNewReportCard("{$deploy->user->username}-{$deploy->id}-{$runId}");
                            $this->dispatch(new ExportDeployCombineJob([$deploy], $reportCard, str_random(16)));
                        }
                    }
                }
 
                JobTracking::changeJobState(JobEntry::SUCCESS, $this->tracking);
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