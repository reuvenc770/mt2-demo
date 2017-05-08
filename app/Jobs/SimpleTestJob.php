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
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(string $foo)
    {
        parent::__construct(self::JOB_NAME);

        $this->foo = $foo;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        parent::handle();

        echo $this->foo;

        $this->runAcceptanceTest();
    }

    /**
     * must return boolean result
     * @return Exception|bool|\Exception
     */
    protected function acceptanceTest(){

        sleep(10);
        try{
            $result = true;
            JobTracking::addDiagnostic(array('acceptance_test' => $result),$this->tracking);
            return $result;
        }catch (Exception $e){
            return $e;
        }

    }
}
