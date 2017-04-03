<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use Tests\TestCase; 

use \App\Console\Commands\DownloadSuppressionFromESPCommand;
use \Symfony\Component\Console\Input\ArrayInput;
use \Symfony\Component\Console\Output\StreamOutput;

class DownloadSuppressionFromESPCommandTest extends TestCase
{
    /**
     * Test for queuing of suppression data pull job.
     *
     * @return void
     */
    public function testSuccessfulJobQueuing()
    {
        $this->expectsJobs( \App\Jobs\DownloadSuppressionFromESP::class );

        #called in the job constructor
        JobTracking::shouldReceive( 'startEspJob' );

        $stub = $this->createMock( \App\Repositories\EspApiAccountRepo::class );
        $stub->method( 'getAccountsByESPName' )
            ->willReturn(
                [ factory( \App\Models\EspAccount::class )->make() ]
            );

        #need to do this since there is a repo injected on creation
        $command = new DownloadSuppressionFromESPCommand( $stub );

        $command->setLaravel( $this->app );
        
        $input = new ArrayInput( [
            'espName' => 'AWeber' ,
            'lookBack' => 1
        ] );

        $tempFile = tmpfile();
        $output = new StreamOutput( $tempFile );

        #this seems to be the standard way to test commands when not using $this->artisan
        $command->run( $input , $output );

        fclose( $tempFile );
    }
}
