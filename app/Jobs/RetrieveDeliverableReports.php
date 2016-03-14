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

    public $processState;
    protected $defaultProcessState = [ "currentFilterIndex" => 0 ];

    protected $currentFilter;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct( $apiName, $espAccountId, $date, $tracking , $processState = null )
    {
        $this->apiName = $apiName;
        $this->espAccountId = $espAccountId;
        $this->date = $date;
        $this->maxAttempts = env('MAX_ATTEMPTS',10);
        $this->tracking = $tracking;

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
        $this->initJobEntry();
        $reportService = APIFactory::createAPIReportService($this->apiName,$this->espAccountId);

        switch ( $this->currentFilter() ) {
            case 'getTickets' :
                $tickets = $reportService->getTickets( $this->espAccountId , $this->date );

                Log::info( json_encode( $tickets ) );

                $this->processState[ 'currentFilterIndex' ]++;

                foreach ( $tickets as $key => $ticket ) {
                    $this->processState[ 'ticket' ] = $ticket;
                    $job = ( new RetrieveDeliverableReports( $this->apiName , $this->espAccountId , $this->date , $this->tracking , $this->processState ) )->delay( 60 ); //Make Longer
                    $this->dispatch( $job );
                }

                $this->changeJobEntry( JobEntry::SUCCESS );
            break;

            case 'getCampaigns' :
                $campaigns = $reportService->getCampaigns( $this->espAccountId , $this->date );

                Log::info( json_encode( $campaigns ) );

                $this->processState[ 'currentFilterIndex' ]++;

                $campaigns->each(function($campaign, $key) {
                    $campaignId = $campaign['internal_id'];
                    $this->processState[ 'campaignId' ] = $campaignId;
                    $this->processState[ 'espId' ] = $this->espAccountId;

                    $job = ( new RetrieveDeliverableReports( $this->apiName, $this->espAccountId, $this->date , $this->tracking , $this->processState ) );
                    $this->dispatch( $job );
                });
                
                $this->changeJobEntry( JobEntry::SUCCESS );
            break;

            case 'processCampaigns' :
                $this->processState[ 'currentFilterIndex' ]++;

                foreach ( [ 'deliveries' , 'opens' , 'clicks' ] as $key => $recordType ) {
                    $this->processState[ 'recordType' ] = $recordType;

                    $job = new RetrieveDeliverableReports( $this->apiName , $this->espAccountId, $this->date , $this->tracking , $this->processState );

                    $this->dispatch( $job );
                }

                $this->changeJobEntry( JobEntry::SUCCESS );
            break;

            case 'saveRecords' :
                $reportService->saveRecords( $this->processState );

                if ( $reportService->shouldRetry() ) {
                    $this->release( 60 ); //Make Longer
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

    protected function initJobEntry () {
        $jobName = self::JOB_NAME . '::' . $this->currentFilter();
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
