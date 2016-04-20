<?php

namespace App\Jobs;

use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use Log;
use App\Models\JobEntry;
use App\Facades\JobTracking;
use App\Factories\APIFactory;
use Illuminate\Foundation\Bus\DispatchesJobs;
use App\Exceptions\JobException;
use Carbon\Carbon;
use App\Models\StandardReport;
use App\Repositories\StandardApiReportRepo;

class RetrieveDeliverableReports extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels, DispatchesJobs;

    CONST JOB_NAME = "RetrieveDeliverableReports";

    protected $apiName;
    protected $espAccountId;
    protected $date;
    protected $maxAttempts;
    protected $tracking;
    protected $reportService;
    protected $standardReportRepo;
    public $defaultQueue;

    public $processState;
    protected $defaultProcessState = [
        "pipe" => 'default' ,
        "currentFilterIndex" => 0
    ];

    protected $currentFilter;

    protected $logTypeMap = [
        JobException::NOTICE => 'notice' ,
        JobException::WARNING => 'warning' ,
        JobException::ERROR => 'error' ,
        JobException::CRITICAL => 'critical'
    ];

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct( $apiName, $espAccountId, $date, $tracking , $processState = null, $defaultQueue = "default")
    {
        $this->apiName = $apiName;
        $this->espAccountId = $espAccountId;
        $this->date = $date;
        $this->maxAttempts = env('MAX_ATTEMPTS',10);
        $this->tracking = $tracking;
        $this->defaultQueue = $defaultQueue;
        $this->reportService = APIFactory::createAPIReportService( $this->apiName,$this->espAccountId );
        $this->standardReportRepo = new StandardApiReportRepo(new StandardReport());

        if ( $processState !== null ) {
            $this->processState = $processState;

            if ( !isset( $this->processState[ 'currentFilterIndex' ] ) ) {
                $this->processState[ 'currentFilterIndex' ] = 0;
            }
        } else {
            $this->processState = $this->defaultProcessState;
        }
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle () {
        $this->initJobEntry();

        $filterName = $this->currentFilter();

        try {
            $this->$filterName();
        } catch ( JobException $e ) {
            $this->logJobException( $e );

            if ( in_array( $e->getCode() , [ JobException::NOTICE , JobException::WARNING ] ) ) {
                $this->releaseJob( $e );
            } else {
                throw $e;
            }
        } catch ( \Exception $e ) {
            $this->logUncaughtException( $e );

            throw $e;
        }
    }

    protected function jobSetup () {
        $this->processState[ 'apiName' ] = $this->apiName;
        $this->processState[ 'espAccountId' ] = $this->espAccountId;
        $this->processState[ 'date' ] = $this->date;
        $this->processState[ 'currentFilterIndex' ]++;

        $this->queueNextJob( $this->defaultQueue );

        $this->changeJobEntry( JobEntry::SUCCESS );
    }

    protected function startTicket () {
        $ticket = $this->reportService->startTicket(
            $this->espAccountId,
            isset($this->processState['campaign']) ? $this->processState['campaign'] : [],
            isset($this->processState['recordType']) ? $this->processState['recordType'] : ''
        );

        $this->processState[ 'currentFilterIndex' ]++;
        $this->processState[ 'ticket' ] = $ticket;

        $this->queueNextJob( $this->defaultQueue , 60 );

        $this->changeJobEntry( JobEntry::SUCCESS );
    }

    protected function checkTicketStatus () {
        $ticketResponse = $this->reportService->checkTicketStatus( $this->processState );

        $this->processState[ 'currentFilterIndex' ]++;
        $this->processState[ 'ticketResponse' ] = $ticketResponse;

        $this->queueNextJob( 'fileDownloads' );

        $this->changeJobEntry( JobEntry::SUCCESS );
    }

    protected function downloadTicketFile () { 
        $filePath = $this->reportService->downloadTicketFile( $this->processState );

        $this->processState[ 'currentFilterIndex' ]++;
        $this->processState[ 'filePath' ] = $filePath;

        $this->queueNextJob( $this->defaultQueue );

        $this->changeJobEntry( JobEntry::SUCCESS );
    }

    protected function getCampaigns () {
        $campaigns = $this->standardReportRepo->getCampaigns( $this->espAccountId , $this->date );

        $this->processState[ 'currentFilterIndex' ]++;

        $campaigns->each( function( $campaign , $key ) {
            $this->processState[ 'campaign' ] = $campaign;
            $this->processState[ 'espId' ] = $this->espAccountId;

            $this->queueNextJob( $this->defaultQueue );
        });
        
        $this->changeJobEntry( JobEntry::SUCCESS );
    }

    protected function splitTypes () {
        $this->processState[ 'currentFilterIndex' ]++;

        $types = $this->reportService->splitTypes( $this->processState );

        foreach ( $types as $index => $currentType ) {
            $this->processState[ 'recordType' ] = $currentType;

            $this->queueNextJob( $this->defaultQueue );
        }

        $this->changeJobEntry( JobEntry::SUCCESS );
    }

    protected function savePaginatedRecords () {
        $map = $this->standardReportRepo->getEspToInternalMap($this->espAccountId);
        $this->reportService->setPageType( $this->processState[ 'recordType' ] );
        $this->reportService->setPageNumber( isset( $this->processState[ 'pageNumber' ] ) ? $this->processState[ 'pageNumber' ] : 1 );

        if ( $this->reportService->pageHasData() ) {
            $this->processState[ 'currentPageData' ] = $this->reportService->getPageData();
            $this->reportService->savePage( $this->processState, $map );
            $this->processState[ 'currentPageData' ] = array();

            $this->reportService->nextPage();

            $this->processState[ 'pageNumber' ] = $this->reportService->getPageNumber();

            $this->queueNextJob( $this->defaultQueue );
        }

        $this->changeJobEntry( JobEntry::SUCCESS );
    }

    protected function saveRecords () {
        if (isset($this->standardReportRepo)) {
            $map = $this->standardReportRepo->getEspToInternalMap($this->espAccountId);
            $this->reportService->saveRecords( $this->processState, $map );

            $this->changeJobEntry( JobEntry::SUCCESS );
        }
        else {
            echo "StandardReportRepo not set. ESP account id " . $this->espAccountId . PHP_EOL;
        }
    }

    protected function getTypeList () {
        $this->processState[ 'currentFilterIndex' ]++;

        $this->processState[ 'typeList' ] = $this->reportService->getTypeList( $this->processState );

        $this->queueNextJob( $this->defaultQueue );

        $this->changeJobEntry( JobEntry::SUCCESS );
    }

    protected function synchronousSaveTypeRecords () {
        if ( !isset( $this->processState[ 'typeList' ] ) ) {
            Log::error( 'typeList not available.' );
            Log::error( $this->getJobInfo() );

            $this->changeJobEntry( JobEntry::FAILED );

            return;
        }

        if ( !isset( $this->processState[ 'typeIndex' ] ) ) {
            $this->processState[ 'typeIndex' ] = 0;
        }

        $currentType = $this->processState[ 'typeList' ][ $this->processState[ 'typeIndex' ] ];

        $this->processState[ 'recordType' ] = $currentType;

        $this->reportService->saveRecords( $this->processState );

        $this->processState[ 'typeIndex' ]++;

        if ( !isset( $this->processState[ 'typeList' ][ $this->processState[ 'typeIndex' ] ] ) ) {
            $this->processState[ 'currentFilterIndex' ]++;
        }

        $this->queueNextJob( $this->defaultQueue );

        $this->changeJobEntry( JobEntry::SUCCESS );
    }

    protected function cleanUp () {
        $this->reportService->cleanUp( $this->processState );

        $this->changeJobEntry( JobEntry::SUCCESS );
    }

    protected function queueNextJob ( $queue = null , $delay = null) {
        $job = new RetrieveDeliverableReports(
            $this->apiName ,
            $this->espAccountId ,
            $this->date ,
            str_random( 16 ) ,
            $this->processState,
            $this->defaultQueue
        );
   
        if ( !is_null( $delay ) ) { $job->delay( $delay ); }
        
        if ( !is_null( $queue ) ) { $job->onQueue( $queue ); }

        $this->dispatch( $job );
    }

    protected function currentFilter () {
        if ( is_null( $this->currentFilter ) ) {
            $filters = config( 'espdeliverables.' . $this->apiName . '.pipes' );
            $pipe = $this->processState[ 'pipe' ];
            $filterIndex = $this->processState[ 'currentFilterIndex' ];

            $this->currentFilter = $filters[ $pipe ][ $filterIndex ];
        }

        return $this->currentFilter;
    }

    protected function releaseJob ( JobException $e ) {
        $this->changeJobEntry( JobEntry::WAITING );

        $this->release( $e->getDelay() );
    }

    protected function initJobEntry () {
        JobTracking::startEspJob( $this->getJobName() ,$this->apiName, $this->espAccountId, $this->tracking);

        echo "\n\n" . Carbon::now() . " - Starting Job: " . $this->getJobName() . "\n";
    }

    protected function changeJobEntry ( $status ) {
        JobTracking::changeJobState( $status , $this->tracking , $this->attempts() );

        if ( $status == JobEntry::SUCCESS ) echo "\n\n\t" . Carbon::now() . " - Finished Job: " . $this->apiName . ':' . $this->espAccountId . ' ' . $this->getJobName() . "\n\n";
        if ( $status == JobEntry::WAITING ) echo "\n\n\t" . Carbon::now() . " - Throwing Job Back into Queue: " . $this->apiName . ':' . $this->espAccountId . ' ' . $this->getJobName() . "\n\n";
    }

    protected function logJobException ( JobException $e ) {
        $logMethod = $this->logTypeMap[ $e->getCode() ];

        Log::$logMethod( str_repeat( '=' , 20 ) );
        Log::$logMethod( '' );
        Log::$logMethod( str_repeat( '=' , 20 ) );
        Log::$logMethod( $e->getMessage() );
        Log::$logMethod( $this->getJobInfo() );
        if ( $e->getCode() > JobException::NOTICE ) { Log::$logMethod( $e->getTraceAsString() ); }
    }

    protected function logUncaughtException ( $e ) {
        Log::critical( str_repeat( '=' , 20 ) );
        Log::critical( '' );
        Log::critical( str_repeat( '=' , 20 ) );
        Log::critical( str_repeat( '#' , 20 ) . 'Uncaught Exception' . str_repeat( '#' , 20 ) );
        Log::critical( $this->getJobInfo() );
    }

    protected function getJobName () {
        return self::JOB_NAME . '::' . $this->currentFilter() . $this->reportService->getUniqueJobId( $this->processState );
    }

    protected function getJobInfo () {
        return 'reports:downloadDeliverables::' . $this->apiName . '::' . $this->espAccountId . '::' . $this->currentFilter() . ' => ' . json_encode( $this->processState ); 
    }

    public function failed()
    {
        Log::critical( 'Job Failed....' );
        Log::critical( $this->getJobInfo() );

        $this->changeJobEntry( JobEntry::FAILED );
    }
}
