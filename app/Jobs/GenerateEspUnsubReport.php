<?php

namespace App\Jobs;

use App\Jobs\Job;
use App\Reports\SuppressionExportReport;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Facades\JobTracking;
use App\Models\JobEntry;
use Log;
class GenerateEspUnsubReport extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    protected $date;
    protected $tracking;
    protected $jobName;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($date, $tracking)
    {
        $this->date = $date;
        $this->tracking = $tracking;
        $this->jobName = "GenerateEspUnsubReport";
        JobTracking::startEspJob($this->jobName, '', 0, $this->tracking);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(SuppressionExportReport $exportReport) {
                JobTracking::changeJobState(JobEntry::RUNNING, $this->tracking);
        try {
            $exportReport->run($this->date);
            JobTracking::changeJobState(JobEntry::SUCCESS, $this->tracking);
        } catch (\Exception $e){
            Log::info($e->getMessage());
            $this->failed();
        }
    }

    public function failed() {
        JobTracking::changeJobState(JobEntry::FAILED, $this->tracking);
    }
}
