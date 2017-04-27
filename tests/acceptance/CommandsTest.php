<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use Tests\TestCase; 

use \Symfony\Component\Console\Input\ArrayInput;
use \Symfony\Component\Console\Output\StreamOutput;

use App\Factories\APIFactory;

//commands to be tested
use \App\Console\Commands\DownloadSuppressionFromESPCommand;

class CommandsTest extends TestCase
{

    protected $frequency=3;

    protected function setUp(){
        parent::setUp();

        $this->clearCache();

        //$this->markTestSkipped('off');
    }


    /**
     * Test DownloadSuppressionFromESPCommand
     *
     * @return void
     */
    public function testDownloadSuppressionFromESPCommand()
    {
        echo "\n\nAPP_ENV:\n";
        echo env( 'APP_ENV' );
        echo "\n";

        $this->expectsJobs( \App\Jobs\DownloadSuppressionFromESP::class ); 

        $max_runtime = 60;
        $espRepo = APIFactory::createESPAPiAccountRepo();

        $command = new DownloadSuppressionFromESPCommand( $espRepo );

        $input = new ArrayInput( [
            'espName' => 'Campaigner' ,
            'lookBack' => 1
        ] );

        //PASS if Job Status == 2, otherwise FAIL
        $this->assertTrue($this->runCommandReturnStatus($command,$input,$max_runtime)==2);
    }


    /**
     * Run the specified Command with the specified input.
     * Monitor the Job Status for the specified runtime threshold
     * Return the Job Status when it reaches value 2 or the
     * runtime threshold has been exceeded.
     * specified
     * @param $command
     * @param $input
     * @param $runtime_threshold
     * @return integer
     */
    public function runCommandReturnStatus($command,$input,$runtime_threshold){

        $command->setLaravel( $this->app );

        //TODO, figure out how to see command output
        $output = new StreamOutput( tmpfile() );

        $command->run( $input,$output);

        $stream = $output->getStream();
        fseek( $stream , 0 );
        $outputString = fread( $stream , 1024 );

        echo "\n\nCommand Output String\n";
        echo $outputString;
        echo "\n";

        $max_tries = floor($runtime_threshold/$this->frequency);
        $tries = 0;

        do{
            sleep($this->frequency);

            echo "\n\n";
            echo "Tracking ID:" . $command->getTrackingId();
            echo "\n";

            $job = JobTracking::getJobProfile($command->getTrackingId());
            print "\njob status: ".$job->status."\n";
            ob_flush();
            $tries++;
        }
        while($job->status!=2 && $tries <= $max_tries);

        return $job->status;

    }

    protected function clearCache()
    {
        $commands = ['clear-compiled', 'cache:clear', 'view:clear', 'config:clear', 'route:clear'];

        foreach ($commands as $command) {
            \Illuminate\Support\Facades\Artisan::call($command);
        }
    }
}
