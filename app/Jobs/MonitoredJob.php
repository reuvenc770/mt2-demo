<?php

namespace App\Jobs;

use App\Models\JobEntry;
use App\Facades\JobTracking;
use App\Console\Commands\Traits\UseTracking;
use Mockery\CountValidator\Exception;


/**
 * Class MonitoredJob
 * @package App\Jobs
 * Handles common job config, setup, teardown, status changes and acceptance test execution
 */
class MonitoredJob extends Job {

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
        $this->tracking = $tracking!=null ? $tracking : $this->getTrackingId();
        $params = array(
            'job_name' => $jobName,
            'runtime_seconds_threshold' => $this->runtime_seconds_threshold
        );
        JobTracking::initiateNewJob($this->tracking,$params);
    }

    /**
     * sets status to RUNNING
     */
    protected function handle(){
        JobTracking::changeJobState(JobEntry::RUNNING,$this->tracking);
    }

    /**
     * executes job acceptance test if exists
     */
    protected function runAcceptanceTest(){

      if(method_exists($this,'acceptanceTest')){
          JobTracking::changeJobState(JobEntry::RUNNING_ACCEPTANCE_TEST,$this->tracking);

          try{
              if($this->acceptanceTest()) JobTracking::changeJobState(JobEntry::SUCCESS,$this->tracking);
              else JobTracking::changeJobState(JobEntry::ACCEPTANCE_TEST_FAILED,$this->tracking);
          }catch(Exception $e){
              $this->failed();
          }

      }else{
          JobTracking::changeJobState(JobEntry::SUCCESS,$this->tracking);
      }

    }

    /**
     * sets status to FAILED
     */
    public function failed()
    {
        JobTracking::changeJobState(JobEntry::FAILED,$this->tracking);
    }
}