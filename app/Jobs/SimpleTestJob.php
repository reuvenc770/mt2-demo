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
 *
 * an example class for a monitored job.
 * The acceptanceTest method is optional, will be executed if it exists.
 * JOB_NAME and $runtimeSecondsThreshold MUST be defined.
 * Job specific tasks are executed in the handleJob method.
 *
 * Uncomment JobTracking::tripUp() for an example of a non-cascading exception that
 * does not fail the job, but leaves it in RUNNING status
 */
class SimpleTestJob extends MonitoredJob implements ShouldQueue
{
    CONST JOB_NAME = "SimpleTestJob";
    protected $foo;

    /**
     * @param  boolean $foo - acceptanceTest result relayed for testing
     */
    public function __construct($foo,$runtimeThreshold=null,$tracking=null)
    {
        parent::__construct(self::JOB_NAME,$runtimeThreshold,$tracking);

        $this->foo = $foo;
    }

    /**
     * Execute the job.
     *
     * example of invoking exception at different layer,
     * stranding the job at running status => added job status update  in handle() catch fixes this.
     * JobTracking::tripUp();
     *
     * @return integer rows affected.
     */
    public function handleJob()
    {

        //do job specific stuff
        echo $this->foo ? "simulating successful acceptance test\n" : "simulating failed acceptance test\n";


        return 0;
    }

    /**
     * must return boolean
     * @return bool
     */
    protected function acceptanceTest(){

        sleep(5); //to observe status ACCEPTANCE_TEST_RUNNING

        //example of how to add a diagnostic
        JobTracking::addDiagnostic(array('acceptance_test' => (integer) $this->foo),$this->tracking);

        return $this->foo;
    }
}
