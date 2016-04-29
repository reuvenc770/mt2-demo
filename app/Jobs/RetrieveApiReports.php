<?php

namespace App\Jobs;

use App\Models\JobEntry;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Facades\JobTracking;
use App\Factories\APIFactory;
use App\Exceptions\JobException;
use Log;

/**
 * Class RetrieveReports
 * @package App\Jobs
 */
class RetrieveApiReports extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;
    CONST JOB_NAME = "RetrieveApiEspReports";
    protected $apiName;
    protected $espAccountId;
    protected $date;
    protected $maxAttempts;
    protected $tracking;

    protected $logTypeMap = [
        JobException::NOTICE => 'notice' ,
        JobException::WARNING => 'warning' ,
        JobException::ERROR => 'error' ,
        JobException::CRITICAL => 'critical'
    ];

    public function __construct($apiName, $espAccountId, $date, $tracking)
    {
       $this->apiName = $apiName;
       $this->espAccountId = $espAccountId;
       $this->date = $date;
       $this->maxAttempts = env('MAX_ATTEMPTS',10);
       $this->tracking = $tracking;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        JobTracking::startEspJob( self::JOB_NAME , $this->apiName , $this->espAccountId , $this->tracking );

        //If it has been retried lets make it wait before it goes back out
        if ($this->attempts() > $this->maxAttempts) {
            $this->release( 1 );
        }

        try {
            $reportService = APIFactory::createAPIReportService( $this->apiName , $this->espAccountId );

            $data = $reportService->retrieveApiStats( $this->date );

            if( $data ){
                $reportService->insertApiRawStats( $data );
            }
        } catch ( JobException $e ) {
            $this->logJobException( $e );

            if ( in_array( $e->getCode() , [ JobException::NOTICE , JobException::WARNING , JobException::ERROR ] ) ) {
                JobTracking::changeJobState( JobEntry::WAITING , $this->tracking , $this->attempts() );

                $this->release( $e->getDelay() );
            } else {
                throw $e;
            }
        } catch ( \Exception $e ) {
            $this->logUncaughtException( $e );

            throw $e;
        }

        JobTracking::changeJobState( JobEntry::SUCCESS , $this->tracking , $this->attempts() );
    }

    protected function logJobException ( JobException $e ) {
        $logMethod = $this->logTypeMap[ $e->getCode() ];

        Log::$logMethod( str_repeat( '=' , 20 ) );
        Log::$logMethod( '' );
        Log::$logMethod( str_repeat( '=' , 20 ) );
        Log::$logMethod( $e->getMessage() );
        Log::$logMethod( $this->getJobInfo() );

        if ( $e->getCode() > JobException::NOTICE ) {
            Log::$logMethod( $e->getFile() );
            Log::$logMethod( $e->getLine() );
            Log::$logMethod( $e->getTraceAsString() );
        }
    }

    protected function logUncaughtException ( $e ) {
        Log::critical( str_repeat( '=' , 20 ) );
        Log::critical( '' );
        Log::critical( str_repeat( '=' , 20 ) );
        Log::critical( str_repeat( '#' , 20 ) . 'Uncaught Exception' . str_repeat( '#' , 20 ) );
        Log::critical( $this->getJobInfo() );
    }

    protected function getJobInfo () {
        return 'reports:downloadApi::' . $this->apiName . '::' . $this->espAccountId; 
    }

    public function failed()
    {
        Log::critical( 'Job Failed....' );
        Log::critical( $this->getJobInfo() );

        JobTracking::changeJobState(JobEntry::FAILED,$this->tracking, $this->maxAttempts);
    }
}
