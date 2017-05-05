<?php

namespace App\Jobs;

use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Facades\JobTracking;
use App\Models\JobEntry;
use App\Jobs\Traits\PreventJobOverlapping;
use App\Services\ListProfileExportService;
use App\DataModels\ReportEntry;
use App\DataModels\CacheReportCard;

class ExportDeployCombineJob extends Job implements ShouldQueue {
    use InteractsWithQueue, SerializesModels, PreventJobOverlapping;
    const BASE_NAME = 'ExportDeployCombine-';
    private $jobName;
    private $deploys;
    private $reportCard;
    private $tracking;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(array $deploys, CacheReportCard $reportCard, $tracking) {
        $this->deploys = $deploys;
        $this->reportCard = $reportCard;
        $this->tracking = $tracking;

        $this->jobName = self::BASE_NAME . $deploy->id;
        JobTracking::startAggregationJob($this->jobName, $this->tracking);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(ListProfileExportService $service) {
        if ($this->jobCanRun($this->jobName)) {
            try {
                $this->createLock($this->jobName);
                JobTracking::changeJobState(JobEntry::RUNNING, $this->tracking);

                foreach ($this->deploys as $deploy) {
                    $entry = new ReportEntry($deploy->name)
                    $entry = $service->createDeployExport($deploy, $entry);
                    $this->reportCard->addEntry($entry);
                }

                $this->reportCard->mail();
                JobTracking::changeJobState(JobEntry::SUCCESS, $this->tracking);
            }
            catch (\Exception $e) {
                echo "{$this->jobName} failed with {$e->getMessage()}  {$e->getLine()}" . PHP_EOL;
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