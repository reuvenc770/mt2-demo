<?php

namespace App\Jobs;

use App\Models\JobEntry;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Facades\JobTracking;
use App\Jobs\Traits\PreventJobOverlapping;
use App\Console\Commands\Traits\UseTracking;
use \Exception;

/**
 * Class MonitoredJob
 * @package App\Jobs
 * Parent class for Jobs. Handles common config and tasks including PID locking,
 * status changes, and acceptance test execution
 */
abstract class MonitoredJob extends Job implements ShouldQueue {
    use InteractsWithQueue, SerializesModels, PreventJobOverlapping;

    use UseTracking;

    protected $tracking;
    protected $jobName;
    protected $diagnostics;

    /**
     * @param $jobName
     * @param null $tracking
     * tracking is generated if not provided. $jobName and $this->runtime_seconds_threshold are required.
     */
    public function __construct($jobName,$tracking=null) {

        $this->jobName = $jobName;
        $this->tracking = $tracking!=null ? $tracking : $this->getTrackingId();
        $params = array(
            'job_name' => $jobName,
            'runtime_seconds_threshold' => $this->runtime_seconds_threshold
        );
        JobTracking::initiateNewMonitoredJob($this->tracking,$params);
    }

    /**
     * Job is PID locked during execution on $jobName.
     * $this->handleJob() defined in the subclass executes the job-specific tasks.
     *
     */
    public function handle() {
        if ($this->jobCanRun($this->jobName)) {
            try {
                $this->createLock($this->jobName);
                JobTracking::changeJobState(JobEntry::RUNNING, $this->tracking);
                echo "{$this->jobName} running" . PHP_EOL;

                $rows = $this->handleJob();

                $rows = $rows ?: 0;

                if($this->runAcceptanceTest()){
                    JobTracking::changeJobState(JobEntry::SUCCESS, $this->tracking, $rows);
                }else{
                    JobTracking::changeJobState(JobEntry::ACCEPTANCE_TEST_FAILED,$this->tracking);
                    JobTracking::addDiagnostic(array('errors' => array('acceptance test failed')),$this->tracking);
                    $this->failed();
                }

            }
            catch (Exception $e) {
                echo "{$this->jobName} failed with {$e->getMessage()}" . PHP_EOL;
                //JobTracking::changeJobState(JobEntry::FAILED, $this->tracking); //commented out for now, but needed since local failed() not called
                JobTracking::addDiagnostic(array('errors' => array('Job FAILED with exception: '.$e->getMessage())),$this->tracking);
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
        echo "running local failed\n";  //never gets here, job_entries.status doesn't set to FAILED
        JobTracking::changeJobState(JobEntry::FAILED, $this->tracking);
    }

    protected function handleJob() {}

    /**
     * executes job acceptance test if it exists, method acceptanceTest().
     */
    protected function runAcceptanceTest(){

        if(method_exists($this,'acceptanceTest')){
            JobTracking::changeJobState(JobEntry::RUNNING_ACCEPTANCE_TEST,$this->tracking);
            if($this->acceptanceTest()){
                return 1;
            }else {
                return 0;
            }
        }
        return 1;
    }
}