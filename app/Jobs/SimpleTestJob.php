<?php

namespace App\Jobs;

use App\Models\JobEntry;
use App\Facades\JobTracking;
use App\Jobs\MonitoredJob;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

/**
 * Class SimpleTestJob
 * @package App\Jobs
 * an example class for a monitored job.
 * The acceptanceTest method is optional, will be executed if it exists.
 * JOB_NAME and $runtime_seconds_threshold MUST be defined.
 * Job specific tasks are executed in the handleJob method.
 *
 * Uncomment JobTracking::tripUp() for an example of a non-cascading exception that
 * does not fail the job, but leaves it in RUNNING status
 */
class SimpleTestJob extends MonitoredJob implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    CONST JOB_NAME = "SimpleTestJob";
    protected $foo;
    protected $runtime_seconds_threshold = 20;

    /**
     * @param  boolean $foo - acceptanceTest result relayed for testing
     */
    public function __construct($foo)
    {
        parent::__construct(self::JOB_NAME);

        $this->foo = $foo;
    }

    /**
     * Execute the job.
     *
     * @return integer, rows affected.
     */
    public function handleJob()
    {

        //do job specific stuff
        echo $this->foo ? 'simulating successful acceptance test' : 'simulating failed acceptance test';

        //example of invoking exception at different layer, stranding the job at running status
        //JobTracking::tripUp();

        return 0;
    }

    /**
     * must return boolean result or exception
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
