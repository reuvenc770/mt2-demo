<?php

namespace App\Jobs;

use DB;
use Log;
use App\Models\JobEntry;
use App\Facades\JobTracking;
use App\Factories\APIFactory;
use App\Exceptions\JobException;
use App\Exceptions\JobAlreadyQueuedException;
use Carbon\Carbon;
use App\Models\StandardReport;
use App\Models\BrontoReport;
use App\Repositories\StandardApiReportRepo;
use App\Exceptions\JobCompletedException;

class RetrieveDeliverableReports extends MonitoredJob
{

    CONST JOB_NAME = "RetrieveDeliverableReports";

    protected $apiName;
    protected $espAccountId;
    protected $date;
    protected $maxAttempts;
    protected $tracking;
    protected $reportService;
    protected $standardReportRepo;
    public $defaultQueue;
    protected $rowCount = 0;
    protected $runtimeThreshold;

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
    public function __construct( $apiName, $espAccountId, $date, $tracking , $processState = null, $defaultQueue = "default", $runtimeThreshold = null)
    {
        $this->apiName = $apiName;
        $this->espAccountId = $espAccountId;
        $this->date = $date;
        $this->maxAttempts = config('jobs.maxAttempts');
        $this->tracking = $tracking;
        $this->defaultQueue = $defaultQueue;
        $this->runTimeThreshold = $this->getCurrentFilterRuntimeThreshold($runtimeThreshold);
        $this->reportService = APIFactory::createAPIReportService( $this->apiName,$this->espAccountId );
        $this->standardReportRepo = new StandardApiReportRepo(new StandardReport());

        if ( $processState !== null ) {
            $this->processState = $processState;

            if ( !isset( $this->processState[ 'currentFilterIndex' ] ) ) {
                $this->processState[ 'currentFilterIndex' ] = 0;
            }
        } else {
            $this->processState = $this->defaultProcessState;
            $this->processState['date'] = $this->date;
        }

        parent::__construct($this->getJobName(),$this->runtimeThreshold,$this->tracking);
        $this->initJobEntry();
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handleJob() {
        if(isset($this->processState['retryFailures']) && $this->processState['retryFailures'] >= 5){
            throw new \Exception("ERROR: 5 process retries failed");
            return;
        }
        try {
            $this->startJobEntry();
            $filterName = $this->currentFilter();
            $this->$filterName();
        } catch (JobCompletedException $e) {
            JobTracking::addDiagnostic(array('warnings' => 'job completed exception thrown'),$this->tracking);
            // killing an attempt at a rerun
            //Log::notice($e->getMessage());//I dont think we need to log this
            exit;
        } catch (JobAlreadyQueuedException $e) {
            JobTracking::addDiagnostic(array('warnings' => 'job already queued exception thrown'),$this->tracking);
            Log::notice($e->getMessage());
            exit;
        } catch ( JobException $e ) {
            $this->logJobException( $e );

            if ( in_array( $e->getCode() , [ JobException::NOTICE , JobException::WARNING , JobException::ERROR ] ) ) {
                $this->releaseJob( $e );
            } else {
                throw $e;
            }
        } catch ( \Exception $e ) {
            $this->logUncaughtException( $e );

            throw $e;
        }

        return $this->rowCount;

    }

    protected function jobSetup () {
        $this->processState[ 'apiName' ] = $this->apiName;
        $this->processState[ 'espAccountId' ] = $this->espAccountId;
        $this->processState[ 'date' ] = $this->date;
        $this->processState[ 'currentFilterIndex' ]++;

        $this->queueNextJob( $this->defaultQueue );

    }

    protected function startTicket () {
        $isRerun = $this->processState['pipe'] === 'rerun';

        $ticket = $this->reportService->startTicket(
            $this->espAccountId,
            isset($this->processState['campaign']) ? $this->processState['campaign'] : [],
            isset($this->processState['recordType']) ? $this->processState['recordType'] : '',
            $isRerun
        );

        $this->processState[ 'currentFilterIndex' ]++;
        $this->processState[ 'ticket' ] = $ticket;

        $this->queueNextJob( $this->defaultQueue , 60 );

    }

    protected function checkTicketStatus () {
        $ticketResponse = $this->reportService->checkTicketStatus( $this->processState );

        $this->processState[ 'currentFilterIndex' ]++;
        $this->processState[ 'ticketResponse' ] = $ticketResponse;

        $this->queueNextJob( 'fileDownloads' );

    }

    protected function downloadTicketFile () { 
        $filePath = $this->reportService->downloadTicketFile( $this->processState );

        $this->processState[ 'currentFilterIndex' ]++;
        $this->processState[ 'filePath' ] = $filePath;

        $this->queueNextJob( $this->defaultQueue );

    }

    protected function getCampaigns () {
        $campaigns = $this->standardReportRepo->getActionsCampaigns( $this->espAccountId , $this->date );

        $this->processState[ 'currentFilterIndex' ]++;

        $campaigns->each( function( $campaign , $key ) {
            $this->processState[ 'campaign' ] = $campaign;
            $this->processState[ 'espId' ] = $this->espAccountId;

            $this->queueNextJob( $this->defaultQueue );
        });
        $rowCount = count($campaigns);
        $this->rowCount = $rowCount;
    }
    
    protected function getDeliverableCampaigns() {
        $campaigns = $this->standardReportRepo->getActionsCampaigns( $this->espAccountId , $this->date );
        $this->processState['recordType'] = 'delivered';
        $this->processState[ 'currentFilterIndex' ]++;

        $campaigns->each( function( $campaign , $key ) {
            $this->processState[ 'campaign' ] = $campaign;
            $this->processState[ 'espId' ] = $this->espAccountId;

            $this->queueNextJob( $this->defaultQueue );
        });
        $rowCount = count($campaigns);
        $this->rowCount = $rowCount;
    }

    protected function getSplitDeliverableCampaigns() {
        $campaigns = $this->standardReportRepo->getActionsCampaigns( $this->espAccountId , $this->date );
        $this->processState['recordType'] = 'delivered';
        $this->processState[ 'currentFilterIndex' ]++;

        $campaigns->each( function( $campaign , $key ) {
            $this->processState[ 'campaign' ] = $campaign;
            $this->processState[ 'espId' ] = $this->espAccountId;
            $realReports = $this->reportService->getRawReportsForSplit($campaign->campaign_name,$this->espAccountId);
            //from the standard we grab all raw with same name. they will all be attributed to the standard report
            foreach($realReports as $report){
                $this->processState['campaign']->esp_internal_id = $report->internal_id;
                $this->queueNextJob( $this->defaultQueue );
            }
        });
        $rowCount = count($campaigns);
        $this->rowCount = $rowCount;
    }

    protected function getRerunCampaigns () {
        $deploys = DB::table('deploy_record_reruns AS drr')
            ->select('external_deploy_id', 'drr.esp_internal_id', 'drr.esp_account_id', 'datetime', 
                'delivers', 'opens', 'clicks', 'unsubs', 'complaints', 'bounces')
            ->join('mt2_reports.standard_reports AS sr', 'drr.deploy_id', '=', 'sr.external_deploy_id')
            ->where('drr.esp_account_id', $this->espAccountId)
            ->orderBy('drr.esp_account_id');

        $this->processState[ 'currentFilterIndex' ]++;

        $deploys->each( function( $deploy , $key ) {
            $this->processState[ 'campaign' ] = $deploy;
            $this->processState[ 'espId' ] = $this->espAccountId;

            $this->queueNextJob( $this->defaultQueue );
        });
        $rowCount = count($deploys);
        $this->rowCount = $rowCount;
    }

    protected function getRawCampaigns () {
        $rawCampaigns = $this->reportService->getRawCampaigns( $this->processState );

        $this->processState[ 'currentFilterIndex' ]++;

        $rawCampaigns->each( function( $campaign , $key ) {
            $this->processState[ 'campaign' ] = $campaign;
            $this->processState[ 'espId' ] = $this->espAccountId;

            $this->queueNextJob( $this->defaultQueue );
        });

        $rowCount = count( $rawCampaigns );
        $this->rowCount = $rowCount;

    }

    protected function getBrontoRerunCampaigns() {

        $deploys = DB::table('deploy_record_reruns AS drr')
            ->select('external_deploy_id', 'drr.esp_internal_id' , 'drr.esp_account_id', 'datetime',
                'delivers', 'opens', 'clicks', 'unsubs', 'complaints', 'bounces')
            ->join('mt2_reports.standard_reports AS sr', 'drr.deploy_id', '=', 'sr.external_deploy_id')
            ->where('drr.esp_account_id', $this->espAccountId)
            ->orderBy('drr.esp_account_id');

        $this->processState[ 'currentFilterIndex' ]++;

        $deploys->each( function( $deploy , $key ) {
            if ( BrontoReport::where( [
                    [ 'id' , '=' , $deploy->esp_internal_id ] ,
                    [ 'type' , '<>' , 'transactional' ] ,
                    [ 'type' , '<>' , 'test' ]
                ] )->whereRaw( 'message_name REGEXP "^[[:digit:]]+\_"' )
                 ->get()->count() > 0
            ) {
                $this->processState[ 'campaign' ] = BrontoReport::find( $deploy->esp_internal_id )->first();
                $this->processState[ 'campaign' ]->delivers = $deploy->delivers;
                $this->processState[ 'campaign' ]->opens = $deploy->opens;
                $this->processState[ 'campaign' ]->clicks = $deploy->clicks;
                $this->processState[ 'campaign' ]->unsubs = $deploy->unsubs;
                $this->processState[ 'campaign' ]->complaints = $deploy->complaints;
                $this->processState[ 'campaign' ]->bounces = $deploy->bounces;

                $this->processState[ 'espId' ] = $this->espAccountId;

                $this->queueNextJob( $this->defaultQueue );
            }
        });
        $rowCount = count($deploys);
        $this->rowCount = $rowCount;

    }

    protected function splitTypes () {
        $this->processState[ 'currentFilterIndex' ]++;

        $types = $this->reportService->splitTypes( $this->processState );
        foreach ( $types as $index => $currentType ) {
            $this->processState[ 'recordType' ] = $currentType;

            $this->queueNextJob( $this->defaultQueue );
        }
    }

    protected function savePaginatedRecords () {
        $rowCount = 0;
        $monthAgo = Carbon::today()->subDays(31);

        # "lt()" is understood as "earlier than" rather than "less than/since"
        # pick the earlier of either the start date or 31 days ago
        $startDate = Carbon::parse($this->date)->lt($monthAgo) ? $date : $monthAgo->toDateString();
        $map = $this->standardReportRepo->getEspToInternalMap($this->espAccountId, $startDate);
        
        $this->reportService->setPageType( $this->processState[ 'recordType' ] );
        $this->reportService->setPageNumber( isset( $this->processState[ 'pageNumber' ] ) ? $this->processState[ 'pageNumber' ] : 1 );

        if ( $this->reportService->pageHasData( $this->processState ) ) {
            $this->processState[ 'currentPageData' ] = $this->reportService->getPageData();
            $this->reportService->savePage( $this->processState, $map );
            $rowCount = count($this->processState[ 'currentPageData' ]);
            $this->processState[ 'currentPageData' ] = array();

            $this->reportService->nextPage();

            $this->processState[ 'pageNumber' ] = $this->reportService->getPageNumber();

            $this->queueNextJob( $this->defaultQueue );
        }
        $this->rowCount = $rowCount;

    }

    protected function savePaginatedAWeberRecords () {
        $rowCount = 0;
        $this->reportService->setPageType( $this->processState[ 'recordType' ] );
        $this->reportService->setPageNumber( isset( $this->processState[ 'pageNumber' ] ) ? $this->processState[ 'pageNumber' ] : 1 );

        if ( $this->reportService->pageHasCampaignData($this->processState) ) {
            $this->processState[ 'currentPageData' ] = $this->reportService->getPageData();
            $rowCount = $this->reportService->savePage( $this->processState);
            $this->processState[ 'currentPageData' ] = array();

            $this->reportService->nextPage();

            $this->processState[ 'pageNumber' ] = $this->reportService->getPageNumber();

            $this->queueNextJob( $this->defaultQueue );
        } else {
            //going back in and flushing out anything old.
            $forceSaveOfLeftOvers = true;
            $this->reportService->savePage( $this->processState,$forceSaveOfLeftOvers);
        }
        $this->rowCount = $rowCount;

    }
    
    protected function saveOpenAWeberRecords(){
        $rowCount = 0;
        $this->reportService->generateOpenRecordData($this->processState);
        $this->processState[ 'currentPageData' ] = $this->reportService->getPageData();
        $rowCount = $this->reportService->savePage( $this->processState);
        $this->rowCount = $rowCount;

    }



    protected function savePaginatedCampaignRecords () {
        $map = $this->standardReportRepo->getEspToInternalMap($this->espAccountId);
        
        $this->reportService->setPageType( $this->processState[ 'recordType' ] );
        $this->reportService->setPageNumber( isset( $this->processState[ 'pageNumber' ] ) ? $this->processState[ 'pageNumber' ] : 1 );

        $rowCount = 0;
        $continue = true;

        while ($continue) {
            if ( $this->reportService->pageHasCampaignData($this->processState)) {
                $this->processState[ 'currentPageData' ] = $this->reportService->getPageData();
                $this->reportService->saveActionPage( $this->processState, $map );
                $rowCount += count($this->processState[ 'currentPageData' ]);
                $this->processState[ 'currentPageData' ] = array();
                $this->reportService->nextPage();
                $this->processState[ 'pageNumber' ] = $this->reportService->getPageNumber();


            }
            else {
                $continue = false;
                $this->processState[ 'currentFilterIndex' ]++;
                if ('rerun' === $this->processState['pipe']) {
                    $this->queueNextJob( $this->defaultQueue );
                }
            }
        }

        $this->rowCount = $rowCount;

    }

    protected function saveRecords () {
        if (isset($this->standardReportRepo)) {
            $map = $this->standardReportRepo->getEspToInternalMap($this->espAccountId);
            $total = $this->reportService->saveRecords( $this->processState, $map );
            $this->rowCount = $total;
        }
        else {
            echo "StandardReportRepo not set. ESP account id " . $this->espAccountId . PHP_EOL;
        }

        if ('rerun' === $this->processState['pipe']) {
            $this->processState[ 'currentFilterIndex' ]++;
            $this->queueNextJob( $this->defaultQueue );
        }
    }

    protected function getTypeList () {
        $this->processState[ 'currentFilterIndex' ]++;

        $this->processState[ 'typeList' ] = $this->reportService->getTypeList( $this->processState );
        $this->processState['recordType'] = $this->processState[ 'typeList' ][0];
        $this->processState[ 'typeIndex' ] = 0;
        $this->queueNextJob( $this->defaultQueue );
    }

    protected function synchronousSaveTypeRecords () {
        if ( !isset( $this->processState[ 'typeList' ] ) ) {
            Log::error( 'typeList not available.' );
            Log::error( $this->getJobInfo() );

            throw new \Exception( 'typeList not available.' );
            return;
        }

        $total = $this->reportService->saveRecords( $this->processState );

        $this->processState[ 'typeIndex' ]++;

        if ( !isset( $this->processState[ 'typeList' ][ $this->processState[ 'typeIndex' ] ] ) ) {
            $this->processState[ 'currentFilterIndex' ]++;
        } else {
            $this->processState[ 'recordType' ] = $this->processState[ 'typeList' ][ $this->processState[ 'typeIndex' ] ];
        }

        $this->queueNextJob( $this->defaultQueue );
        $this->rowCount = $total;
    }

    protected function cleanUp () {
        $this->reportService->cleanUp( $this->processState );
    }

    protected function queueNextJob ( $queue = null , $delay = null) {
        
        $job = new RetrieveDeliverableReports(
            $this->apiName ,
            $this->espAccountId ,
            $this->date ,
            str_random( 16 ) ,
            $this->processState,
            $this->defaultQueue,
            $this->runtimeThreshold
        );
   
        if ( !is_null( $delay ) ) { $job->delay( $delay ); }
        
        if ( !is_null( $queue ) ) { $job->onQueue( $queue ); }

        $this->dispatch( $job );
    }

    protected function removeDeploys() {
        DB::table('deploy_record_reruns')
            ->where('deploy_id', $this->processState['campaign']->external_deploy_id)
            ->delete();
        $rowTotal = 1;
        $this->rowCount = $rowTotal;
    }

    protected function currentFilter () {
        if ( is_null( $this->currentFilter ) ) {
            $filters = config( 'espdeliverables.' . $this->apiName . '.pipes' );
            $pipe = $this->processState[ 'pipe' ];
            $filterIndex = $this->processState[ 'currentFilterIndex' ];

            $this->currentFilter = $filters[ $pipe ][ $filterIndex ]['name'];
        }

        return $this->currentFilter;
    }

    protected function getCurrentFilterRuntimeThreshold($lastRuntimeThreshold) {
            $filters = config( 'espdeliverables.' . $this->apiName . '.pipes' );
            $pipe = $this->processState[ 'pipe' ];
            $filterIndex = $this->processState[ 'currentFilterIndex' ];

            if(isset($filters[ $pipe ][ $filterIndex ]['runtimeThreshold'])){
               return $filters[ $pipe ][ $filterIndex ]['runtimeThreshold'];
            }else{
               return $lastRuntimeThreshold;
            }
    }

    protected function releaseJob ( JobException $e ) {
        $this->changeJobEntry( JobEntry::WAITING );

        $this->release( $e->getDelay() );
    }

    protected function initJobEntry () {
        $campaignId = 0;
        if(isset($this->processState['campaign'])){
           $campaignId = $this->processState['campaign']->esp_internal_id;
        }

        if (isset($this->processState['pipe']) 
            && 'rerun' === $this->processState['pipe'] 
            && 0 !== $campaignId
            && is_integer($campaignId)) { // temporary workaround for Bronto

            $name = $this->getJobName();

            if(JobTracking::isRerunJobAlreadyQueued($name, $campaignId)) {
                throw new JobAlreadyQueuedException("Job $name already queued");
            }
        }
        
        if ( '' === $campaignId ) {
            $campaignId = 0;
        }

        JobTracking::startEspJob( $this->getJobName() ,$this->apiName, $this->espAccountId, $this->tracking, $campaignId);
        echo "\n\n" . Carbon::now() . " - Queuing Job: " . $this->getJobName() . "\n";
    }

    protected function startJobEntry () {

        echo "\n\n" . Carbon::now() . " - Queuing Job: " . $this->getJobName() . $this->tracking . "\n";
    }

    protected function changeJobEntry ( $status, $totalRows = 0 ) {
        JobTracking::changeJobState( $status , $this->tracking, $totalRows);

        if ( $status == JobEntry::SUCCESS ) echo "\n\n\t" . Carbon::now() . " - Finished Job: " . $this->apiName . ':' . $this->espAccountId . ' ' . $this->getJobName() . "\n\n";
        if ( $status == JobEntry::WAITING ) echo "\n\n\t" . Carbon::now() . " - Throwing Job Back into Queue: " . $this->apiName . ':' . $this->espAccountId . ' ' . $this->getJobName() . "\n\n";
    }

    protected function logJobException ( JobException $e ) {
        $logMethod = isset($this->logTypeMap[ $e->getCode() ]) ? $this->logTypeMap[ $e->getCode() ] : "error" ;

        Log::$logMethod( str_repeat( '=' , 20 ) );
        Log::$logMethod( '' );
        Log::$logMethod( str_repeat( '=' , 20 ) );
        Log::$logMethod( $e->getMessage() );
        Log::$logMethod( $this->getJobInfo() );
        if ( $e->getCode() >= JobException::NOTICE ) {
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

    protected function getJobName () {
        $type = "";
        $rerun = '';
        if(isset($this->processState['recordType'])){
            $type = $this->processState['recordType'];
        }
        if ('rerun' === $this->processState['pipe']) {
            $rerun = '-rerun';
        }
        return self::JOB_NAME . '::' . $this->apiName. '::'. $this->espAccountId . '::' . $this->currentFilter() . $this->reportService->getUniqueJobId( $this->processState ). "::". $type . $rerun;
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

