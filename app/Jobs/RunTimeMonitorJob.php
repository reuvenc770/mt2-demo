<?php

namespace App\Jobs;

use App\Models\JobEntry;
use App\Facades\JobTracking;
use App\Jobs\MonitoredJob;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class RunTimeMonitorJob extends MonitoredJob implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    CONST JOB_NAME = "JobsRunTimeMonitor";
    protected $runtime_seconds_threshold = 360;

    /**
     * Create a new runtime monitor instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct(self::JOB_NAME);
    }

    /**
     * .
     *
     * @return void
     */
    public function handle()
    {
        parent::handle();

        //check for still running runtime monitor

        //if still running, check its runtime
        // throw alert if beyond threshold
        // else exit

        //pull all jobs still running that have specified runtime thresholds
        //(NOTE: we need to make sure all non-running jobs have time_finished not null

        //check each job and set appropriate statuses and send out appropriate alerts
    }

    /**
     * will elaborate on this later
     * @return Exception|bool|\Exception
     */
    protected function acceptanceTestOff(){
        return true;

    }
}
