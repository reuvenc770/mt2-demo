<?php

namespace App\Jobs;

use App\Models\JobEntry;
use App\Facades\JobTracking;
use App\Jobs\MonitoredJob;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class SimpleTestJob extends MonitoredJob implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    CONST JOB_NAME = "SimpleTestJob";
    protected $foo;
    protected $runtime_seconds_threshold = 20;

    /**
     * @param  boolean $foo - acceptanceTest result sent for testing
     */
    public function __construct($foo)
    {
        parent::__construct(self::JOB_NAME);

        $this->foo = $foo;
    }

    /**
     * Execute the job.
     *
     * @return boolean
     */
    public function handleJob()
    {

        //do job specific stuff, then call the parent handleJob to run the acceptance test

        parent::handleJob();

        return 1;
    }

    /**
     * must return boolean result
     * @return Exception|bool|\Exception
     */
    protected function acceptanceTest(){

        sleep(5); //to observe status ACCEPTANCE_TEST_RUNNING
        try{
            $result = $this->foo;
            //example of how to add a diagnostic
            JobTracking::addDiagnostic(array('acceptance_test' => (integer) $result),$this->tracking);
            return $result;
        }catch (Exception $e){
            return $e;
        }

    }
}
