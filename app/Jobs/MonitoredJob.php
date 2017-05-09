<?php

namespace App\Jobs;

use App\Models\JobEntry;
use App\Facades\JobTracking;
use App\Console\Commands\Traits\UseTracking;
use Mockery\CountValidator\Exception;
use Symfony\Component\Debug\ExceptionHandler;


/**
 * Class MonitoredJob
 * @package App\Jobs
 * Handles common job config, setup, tear down, and acceptance test execution
 */
class MonitoredJob extends SafeJob {

    use UseTracking;

    protected $tracking;
    protected $jobName;
    protected $runtime_seconds_threshold = 60;
    protected $diagnostics = array();

    /**
     * @param $jobName
     * @param null $tracking
     * provides tracking if none provided, creates job
     */
    public function __construct($jobName,$tracking=null){
        $this->jobName = $jobName;
        $this->tracking = $tracking!=null ? $tracking : $this->getTrackingId();
        $params = array(
            'job_name' => $jobName,
            'runtime_seconds_threshold' => $this->runtime_seconds_threshold
        );
        JobTracking::initiateNewMonitoredJob($this->tracking,$params);
    }

    public function handleJob(){
        if(!$this->runAcceptanceTest()) throw new Exception('acceptance test failed');
    }

    /**
     * executes job acceptance test if it exists
     */
    protected function runAcceptanceTest(){

        if(method_exists($this,'acceptanceTest')){
            JobTracking::changeJobState(JobEntry::RUNNING_ACCEPTANCE_TEST,$this->tracking);
            try{
                if($this->acceptanceTest()){
                    return 1;
                }else {
                    throw new Exception('acceptance test failed');
                }
            }catch(Exception $e){
                JobTracking::changeJobState(JobEntry::ACCEPTANCE_TEST_FAILED,$this->tracking);
                $this->failed();
                return 0;
            }

        }else{
            return 1;
        }

    }

    public function failed() {
        if(JobTracking::getJobState($this->tracking)!=JobEntry::ACCEPTANCE_TEST_FAILED){
            JobTracking::changeJobState(JobEntry::FAILED, $this->tracking);
        }
    }
}