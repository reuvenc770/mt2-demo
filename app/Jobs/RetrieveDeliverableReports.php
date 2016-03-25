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
    public function handle()
    {
        Log::info("Job Tries at start {$this->attempts()}");
        Log::info( $this->apiName . '::' . $this->espAccountId . '::' . $this->currentFilter() . ' => ' . json_encode( $this->processState ) );

        $reportService = APIFactory::createAPIReportService($this->apiName,$this->espAccountId);

        switch ( $this->currentFilter() ) {
            case 'getTickets' :
                $this->initJobEntry();

                $tickets = $reportService->getTickets( $this->espAccountId , $this->date );

                $this->processState[ 'currentFilterIndex' ]++;

                foreach ( $tickets as $key => $ticket ) {
                    $this->processState[ 'ticket' ] = $ticket;
                    $job = ( new RetrieveDeliverableReports(
                        $this->apiName ,
                        $this->espAccountId ,
                        $this->date ,
                        str_random( 16 ) ,
                        $this->processState,
                        $this->queue
                    ) )->delay( 60 )->onQueue($this->queue); //Make Longer

                    $this->dispatch( $job );
                }

                $this->changeJobEntry( JobEntry::SUCCESS );
            break;

            case 'getCampaigns' :
                $this->initJobEntry();

                $campaigns = $reportService->getCampaigns( $this->espAccountId , $this->date );

                $this->processState[ 'currentFilterIndex' ]++;

                $campaigns->each( function( $campaign , $key ) {
                    $campaignId = $campaign['internal_id'];
                    $this->processState[ 'campaignId' ] = $campaignId;
                    $this->processState[ 'espId' ] = $this->espAccountId;

                    $job = (new RetrieveDeliverableReports(
                        $this->apiName,
                        $this->espAccountId,
                        $this->date ,
                        str_random( 16 ) ,
                        $this->processState,
                        $this->queue
                    ))->onQueue($this->queue);

                    $this->dispatch( $job );
                });
                
                $this->changeJobEntry( JobEntry::SUCCESS );
            break;

            case 'splitTypes' :
                $this->initJobEntry( $reportService->getUniqueJobId( $this->processState ) );

                $this->processState[ 'currentFilterIndex' ]++;

                $types = $reportService->splitTypes();

                foreach ( $types as $index => $currentType ) {
                    Log::info( 'Creating job for type ' . $currentType );

                    $this->processState[ 'recordType' ] = $currentType;

                    $job = (new RetrieveDeliverableReports(
                        $this->apiName ,
                        $this->espAccountId ,
                        $this->date ,
                        str_random( 16 ) ,
                        $this->processState,
                        $this->queue
                    ))->onQueue($this->queue);

                    $this->dispatch( $job );
                }

                $this->changeJobEntry( JobEntry::SUCCESS );
            break;

            case 'savePaginatedRecords' :
                $this->initJobEntry( $reportService->getUniqueJobId( $this->processState ) );
                
                $reportService->setPageType( $this->processState[ 'recordType' ] );
                $reportService->setPageNumber( isset( $this->processState[ 'pageNumber' ] ) ? $this->processState[ 'pageNumber' ] : 1 );

                if ( $reportService->pageHasData() ) {
                    $this->processState[ 'currentPageData' ] = $reportService->getPageData();
                    $reportService->saveRecords( $this->processState );
                    $this->processState[ 'currentPageData' ] = array();

                    $reportService->nextPage();

                    $this->processState[ 'pageNumber' ] = $reportService->getPageNumber();

                    $job = (new RetrieveDeliverableReports(
                        $this->apiName,
                        $this->espAccountId,
                        $this->date ,
                        str_random( 16 ) ,
                        $this->processState ,
                        $this->queue
                    ))->onQueue($this->queue);

                    $this->dispatch( $job );
                }

                $this->changeJobEntry( JobEntry::SUCCESS );
            break;

            case 'saveRecords' :
                $this->initJobEntry( $reportService->getUniqueJobId( $this->processState ) );

                $reportService->saveRecords( $this->processState );

                if ( $reportService->shouldRetry() ) {
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
            break;
        }
    }

    protected function currentFilter () {
        if ( is_null( $this->currentFilter ) ) {
            $filters = config( 'espdeliverables.' . $this->apiName . '.filters' );
            $this->currentFilter = $filters[ $this->processState[ 'currentFilterIndex' ] ];
        }

        return $this->currentFilter;
    }

    protected function initJobEntry ( $jobId = '' ) {
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
