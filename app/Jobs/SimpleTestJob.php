<?php

namespace App\Jobs;

use App\Jobs\MonitoredJob;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use \PHPUnit_TextUI_TestRunner;
use Tests\Acceptance\SimpleTest;

class SimpleTestJob extends MonitoredJob implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    protected $foo;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(string $foo)
    {
        $this->foo = $foo;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        echo $this->foo;
        $this->acceptanceTest();
    }

    private function acceptanceTest(){

        $phpunit = new PHPUnit_TextUI_TestRunner;
        $test = new SimpleTest();

        try {
            $test_results = $phpunit->dorun($test);
        } catch (PHPUnit_Framework_Exception $e) {
            print $e->getMessage() . "\n";
            die ("Unit tests failed.");
        }
    }
}
