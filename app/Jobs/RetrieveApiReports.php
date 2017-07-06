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
class RetrieveApiReports extends MonitoredJob implements ShouldQueue
{
    CONST JOB_NAME = "RetrieveApiEspReports";
    protected $apiName;
    protected $espAccountId;
    protected $date;
    protected $attempts;
    protected $tracking;
    protected $apiLimit;

    protected $logTypeMap = [
        JobException::NOTICE => 'notice' ,
        JobException::WARNING => 'warning' ,
        JobException::ERROR => 'error' ,
        JobException::CRITICAL => 'critical'
    ];

    public function __construct($runtimeThreshold,$apiName, $espAccountId, $date, $tracking, $apiLimit = null)
    {

       $jobname = self::JOB_NAME."_".$apiName."_".$espAccountId;
       parent::__construct($jobname,$runtimeThreshold,$tracking);

       $this->apiName = $apiName;
       $this->espAccountId = $espAccountId;
       $this->date = $date;
       $this->attempts = 0;
       $this->tracking = $tracking;
       $this->apiLimit = $apiLimit;
       JobTracking::startEspJob( $jobname , $this->apiName , $this->espAccountId , $this->tracking );
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handleJob()
    {

        JobTracking::changeJobState( JobEntry::RUNNING , $this->tracking);
        $count = 0;
        try {
            $reportService = APIFactory::createAPIReportService( $this->apiName , $this->espAccountId );

            if ( !is_null( $this->apiLimit ) ) {
                $reportService->setRetrieveApiLimit( $this->apiLimit );
            }

            $data = $reportService->retrieveApiStats( $this->date );

            if( $data ){
                $reportService->insertApiRawStats( $data );
                $count = count($data);
            }
            JobTracking::changeJobState( JobEntry::SUCCESS , $this->tracking , $count);
        } catch ( JobException $e ) {
            $this->logJobException( $e );

            if ( in_array( $e->getCode() , [ JobException::NOTICE , JobException::WARNING , JobException::ERROR ] ) ) {
                JobTracking::changeJobState( JobEntry::WAITING , $this->tracking);

                $this->release( $e->getDelay() );
            } else {
                throw $e;
            }
        } catch ( \Exception $e ) {
            $this->logUncaughtException( $e );

            throw $e;
        }
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

        JobTracking::changeJobState(JobEntry::FAILED,$this->tracking);
    }
}
