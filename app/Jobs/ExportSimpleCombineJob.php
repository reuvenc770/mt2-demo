<?php

namespace App\Jobs;

use App\Models\JobEntry;
use App\Services\ListProfileCombineService;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Facades\JobTracking;
use App\Jobs\Traits\PreventJobOverlapping;
use App\Services\ListProfileService;
use App\Services\ListProfileScheduleService;
use App\Services\ListProfileExportService;
use App\DataModels\ReportEntry;
use App\DataModels\CacheReportCard;

class ExportSimpleCombineJob extends Job implements ShouldQueue {
    use InteractsWithQueue, SerializesModels, PreventJobOverlapping;

    private $tracking;
    private $combineId;
    private $jobName;
    private $reportCard;

    public function __construct($combineId, CacheReportCard $reportCard, $tracking) {
        $this->combineId = $combineId;
        $this->reportCard = $reportCard;
        $this->tracking = $tracking;
        $this->jobName = 'ExportCombine-' . $combineId;
        JobTracking::startAggregationJob($this->jobName, $this->tracking);
    }

    /**
     * Build a table for each list profile against each offer it used in.
     * 0 for offer is passed for scheduled list exports
     * @param ListProfileService $service
     * @param ListProfileScheduleService $schedule
     */
    public function handle(ListProfileService $service, 
        ListProfileScheduleService $schedule, 
        ListProfileCombineService $combineService, 
        ListProfileExportService $exportService) {

        if ($this->jobCanRun($this->jobName)) {
            try {
                $this->createLock($this->jobName);
                JobTracking::changeJobState(JobEntry::RUNNING, $this->tracking);

                $combine = $combineService->getCombineById($this->combineId);

                // This might not be strictly necessary
                // The only case where it might be is if this combine 
                // uses a list profile that is not updated daily
                foreach($combine->listProfiles as $listProfile) {
                    $service->buildProfileTable($listProfile->id);
                    $schedule->updateSuccess($listProfile->id);
                }

                $entry = new ReportEntry($combine->name);

                $entry = $exportService->createSimpleCombineExport($combine, $entry);
                $this->reportCard->addEntry($entry);

                $this->reportCard->mail();
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