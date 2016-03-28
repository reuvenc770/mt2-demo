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
    public $queue;

    public $processState;
    protected $defaultProcessState = [ "currentFilterIndex" => 0 ];

    protected $currentFilter;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct( $apiName, $espAccountId, $date, $tracking , $processState = null, $queue = "default")
    {
        $this->apiName = $apiName;
        $this->espAccountId = $espAccountId;
        $this->date = $date;
        $this->maxAttempts = env('MAX_ATTEMPTS',10);
        $this->tracking = $tracking;
        $this->queue = $queue;
        $this->reportService = APIFactory::createAPIReportService( $this->apiName,$this->espAccountId );

        if ( $processState !== null ) {
            $this->processState = $processState;
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

        $this->$filterName();
    }

    protected function jobSetup () {
        $this->processState[ 'apiName' ] = $this->apiName;
        $this->processState[ 'espAccountId' ] = $this->espAccountId;
        $this->processState[ 'date' ] = $this->date;
        $this->processState[ 'currentFilterIndex' ]++;

        $this->queueNextJob();

        $this->changeJobEntry( JobEntry::SUCCESS );
    }

    protected function startTicket () {
        $ticket = $this->reportService->startTicket(
            $this->espAccountId ,
            isset( $this->processState[ 'campaign' ] ) ? $this->processState[ 'campaign' ] : [] ,
            isset( $this->processState[ 'recordType' ] ) ? $this->processState[ 'recordType' ] : ''
        );

        $this->processState[ 'currentFilterIndex' ]++;
        $this->processState[ 'ticket' ] = $ticket;

        $this->queueNextJob( null , 60 );

        $this->changeJobEntry( JobEntry::SUCCESS );
    }

    protected function checkTicketStatus () {
        $ticketResponse = $this->reportService->checkTicketStatus( $this->processState );

        if ( $ticketResponse === false ) {
            $this->changeJobEntry( JobEntry::WAITING );

            $this->release( 60 );
        } else {
            $this->processState[ 'currentFilterIndex' ]++;
            $this->processState[ 'ticketResponse' ] = $ticketResponse;

            $this->queueNextJob( 'fileDownloads' );

            $this->changeJobEntry( JobEntry::SUCCESS );
        }
    }

    protected function downloadTicketFile () { 
        $filePath = $this->reportService->downloadTicketFile( $this->processState );

        if ( $filePath === false ) {
            $this->changeJobEntry( JobEntry::WAITING );

            $this->release( 60 );
        } else {
            $this->processState[ 'currentFilterIndex' ]++;
            $this->processState[ 'filePath' ] = $filePath;

            $this->queueNextJob( 'default' );

            $this->changeJobEntry( JobEntry::SUCCESS );
        }
    }

    protected function getCampaigns () {
        $campaigns = $this->reportService->getCampaigns( $this->espAccountId , $this->date );

        $this->processState[ 'currentFilterIndex' ]++;

        $campaigns->each( function( $campaign , $key ) {
            $this->processState[ 'campaign' ] = $campaign;
            $this->processState[ 'espId' ] = $this->espAccountId;

            $this->queueNextJob();
        });
        
        $this->changeJobEntry( JobEntry::SUCCESS );
    }

    protected function splitTypes () {
        $this->processState[ 'currentFilterIndex' ]++;

        $types = $this->reportService->splitTypes();

        foreach ( $types as $index => $currentType ) {
            $this->processState[ 'recordType' ] = $currentType;

            $this->queueNextJob();
        }

        $this->changeJobEntry( JobEntry::SUCCESS );
    }

    protected function savePaginatedRecords () {
        $this->reportService->setPageType( $this->processState[ 'recordType' ] );
        $this->reportService->setPageNumber( isset( $this->processState[ 'pageNumber' ] ) ? $this->processState[ 'pageNumber' ] : 1 );

        if ( $this->reportService->pageHasData() ) {
            $this->processState[ 'currentPageData' ] = $this->reportService->getPageData();
            $this->reportService->saveRecords( $this->processState );
            $this->processState[ 'currentPageData' ] = array();

            $this->reportService->nextPage();

            $this->processState[ 'pageNumber' ] = $this->reportService->getPageNumber();

            $this->queueNextJob();
        }

        $this->changeJobEntry( JobEntry::SUCCESS );
    }

    protected function saveRecords () {
        $this->reportService->saveRecords( $this->processState );

        if ( $this->reportService->shouldRetry() ) {
            if ( isset( $this->processState[ 'delay' ] ) ) {
                Log::info("Job Tries {$this->attempts()}");

                $this->changeJobEntry( JobEntry::WAITING );
                $this->release( $this->processState[ 'delay' ] );
            } else {
                Log::info("Job Tries {$this->attempts()}");

                $this->changeJobEntry( JobEntry::WAITING );
                $this->release( 60 );
            }
        } else {
            $this->changeJobEntry( JobEntry::SUCCESS );
        }
    }

    protected function queueNextJob ( $queue = null , $delay = null) {
        $job = new RetrieveDeliverableReports(
            $this->apiName ,
            $this->espAccountId ,
            $this->date ,
            str_random( 16 ) ,
            $this->processState,
            $this->queue
        );
   
        if ( !is_null( $delay ) ) { $job->delay( $delay ); }
        
        if ( !is_null( $queue ) ) { $job->onQueue( $queue ); }

        $this->dispatch( $job );
    }

    protected function currentFilter () {
        if ( is_null( $this->currentFilter ) ) {
            $filters = config( 'espdeliverables.' . $this->apiName . '.filters' );
            $this->currentFilter = $filters[ $this->processState[ 'currentFilterIndex' ] ];
        }

        return $this->currentFilter;
    }

    protected function initJobEntry () {
        Log::info( '' );
        Log::info("Job Tries at start {$this->attempts()}");
        Log::info( $this->apiName . '::' . $this->espAccountId . '::' . $this->currentFilter() . ' => ' . json_encode( $this->processState ) );

        $jobId = $this->reportService->getUniqueJobId( $this->processState );

        $jobName = self::JOB_NAME . '::' . $this->currentFilter() . $jobId;
        JobTracking::startEspJob( $jobName ,$this->apiName, $this->espAccountId, $this->tracking);
    }

    protected function changeJobEntry ( $status ) {
        JobTracking::changeJobState($status,$this->tracking, $this->attempts() );
    }

    public function failed()
    {
        $this->changeJobEntry( JobEntry::FAILED );
    }
}
