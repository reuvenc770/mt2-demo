<?php

namespace App\Jobs;

use App\Models\JobEntry;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Facades\JobTracking;
use App\Jobs\Traits\PreventJobOverlapping;
use App\Console\Commands\Traits\UseTracking;
use Log;

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
    protected $runtimeSecondsThreshold;

    /**
     * @param $jobName
     * @param $runtimeThreshold
     * @param null $tracking
     * tracking is generated if not provided. $jobName and $this->runtimeSecondsThreshold are required.
     */
    public function __construct($jobName,$runtimeThreshold,$tracking=null) {

        $this->runtimeSecondsThreshold = $this->parseRuntimeThreshold($runtimeThreshold);
        $this->jobName = $jobName;
        $this->runtimeSecondsThreshold = $this->runtimeSecondsThreshold;
        $this->tracking = $tracking!=null ? $tracking : $this->getTrackingId();
        $params = array(
            'job_name' => $jobName,
            'runtime_seconds_threshold' => $this->runtimeSecondsThreshold
        );
        JobTracking::initiateNewMonitoredJob($this->tracking,$params);
    }

    /**
     * Job is PID locked during execution on $jobName.
     * $this->handleJob() defined in the subclass executes the job-specific tasks.
     *
     */
    public function handle() {

        if(isset($this->job) && $this->job->getQueue()!=null && $this->job->getQueue()!='default'){
            JobTracking::saveJob($this->tracking,array('queue' => $this->job->getQueue()));
        }

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
                    JobTracking::addDiagnostic(array('errors' => array('acceptance test failed')),$this->tracking);
                    JobTracking::changeJobState(JobEntry::ACCEPTANCE_TEST_FAILED,$this->tracking);
                }
            }
            catch (\Exception $e) {
                echo "{$this->jobName} failed with {$e->getMessage()}" . PHP_EOL;
                JobTracking::addDiagnostic(array('errors' => array('Job FAILED with exception: '.$e->getMessage())),$this->tracking);
                Log::error( 'MonitorJob ERROR: job_entries.tracking="'.$this->tracking.'"' );
                Log::error( $e->getMessage() );
                Log::error( $e->getTraceAsString() );
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

    /**
     * validates runtimeThreshold and converts to seconds
     * examples of valid values: 2h (2 hours), 45m (45 minutes), 368s (368 seconds), 3292 (3292 seconds)
     * @param $runtimeThreshold
     * @return integer
     * @throws \Exception
     */
    private function parseRuntimeThreshold($runtimeThreshold){
        if(!preg_match("/^([0-9]{1,})(s|m|h|)$/",$runtimeThreshold,$rtparts)){
            throw new \Exception("invalid runtimeThreshold");
        }

        if($rtparts[2]=="m") return $rtparts[1]*60;
        elseif($rtparts[2]=="h") return $rtparts[1]*60*60;
        else return $rtparts[1];
    }
}