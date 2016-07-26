<?php

namespace App\Jobs;

use App\Jobs\Job;

use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Facades\JobTracking;
use App\Models\JobEntry;
use App\Jobs\Traits\PreventJobOverlapping;
use App\Factories\ReportFactory;

class ExportActionsJob extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels, PreventJobOverlapping;

    protected $reportName;
    protected $date;
    protected $tracking;
    protected $jobName;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($reportName, $date, $tracking) {
        $this->reportName = $reportName;
        $this->date = $date;
        $this->tracking = $tracking;
        $this->jobName = $reportName . '-' . $date;

        JobTracking::startEspJob($this->jobName, '', 0, $this->tracking);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle() {

        if ($this->jobCanRun($this->jobName)) {
            try {
                $this->createLock($this->jobName);
                JobTracking::changeJobState(JobEntry::RUNNING, $this->tracking);
                echo "{$this->jobName} running" . PHP_EOL;
                $exportReportService = ReportFactory::createReport($this->reportName);
                $exportReportService->execute($this->date);
                $exportReportService->notify();

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
