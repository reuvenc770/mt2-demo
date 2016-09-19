<?php

namespace App\Jobs;

use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Exceptions\JobException;
use App\Jobs\Traits\PreventJobOverlapping;
use Illuminate\Foundation\Bus\DispatchesJobs;

use App\Facades\JobTracking;
use App\Models\JobEntry;

use App\Factories\ServiceFactory;

use Log;
use Carbon\Carbon;

class AttributionAggregatorJob extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels, PreventJobOverlapping, DispatchesJobs;

    const DEFAULT_QUEUE_NAME = 'default'; #'modelAttribution';

    private $jobName = 'AttributionAggregator';
    private $tracking;

    private $reportType;
    private $aggregator;
    private $dateRange;
    private $modelId;
    private $chainOptions;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct( $reportType , $tracking , array $dateRange = null , $modelId = null , $chainOptions = [] )
    {
        $this->jobName .= ":{$reportType}:" . ( is_null( $dateRange ) ? Carbon::today()->toDateString() : $dateRange[ 'start' ] . "-" . $dateRange[ 'end' ] );

        $this->reportType = $reportType;
        $this->tracking = $tracking;
        $this->dateRange = $dateRange;

        $this->modelId = $modelId;

        $this->chainOptions = $chainOptions;

        JobTracking::startAggregationJob( $this->jobName , $this->tracking );
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if ( $this->jobCanRun( $this->jobName ) ) {
            try {
                $this->createLock($this->jobName);

                JobTracking::changeJobState(JobEntry::RUNNING,$this->tracking);

                $this->aggregator = ServiceFactory::createAggregatorService( $this->reportType );
                $this->aggregator->setChainOptions( $this->chainOptions );

                if ( $this->modelId > 0 ) {
                    $this->aggregator->setModelId( $this->modelId );
                }

                $this->aggregator->buildAndSaveReport( $this->dateRange );

                $this->queueNextJob( $this->reportType , $this->dateRange );

                JobTracking::changeJobState( JobEntry::SUCCESS , $this->tracking );
            } catch ( JobException $e ) {
                $this->logJobException( $e );

                throw $e;
            } catch ( \Exception $e ) {
                $this->logUncaughtException( $e );

                throw $e;
            } finally {
                $this->unlock( $this->jobName );
            }
        } else {
            echo "Still running {$this->jobName} - job level" . PHP_EOL;

            JobTracking::changeJobState( JobEntry::SKIPPED , $this->tracking );
        }
    }

    protected function queueNextJob ( $reportType , $dateRange ) {
        $isSingleDayRun = ( Carbon::parse( $dateRange[ 'start' ] )->diffInDays( Carbon::parse( $dateRange[ 'end' ] ) ) === 0 );

        if ( $isSingleDayRun ) {
            $nextReportType = "";

            switch ( $reportType ) {
                case 'Record' :
                    $nextReportType = 'Feed';
                break;

                case 'Feed' :
                    $nextReportType = 'Client';
                break;

                case 'Client' :
                    $this->dispatch( new AttributionConversionJob(
                        $this->chainOptions[ 'processMode' ] ,
                        'all' ,
                        str_random( 16 ) , 
                        $this->chainOptions[ 'dateRange' ] ,
                        $this->chainOptions[ 'currentDate' ]
                    ) );

                    JobTracking::changeJobState( JobEntry::SUCCESS , $this->tracking );
 
                    $this->unlock( $this->jobName );
 
                    exit();
                break;

                default :
                    JobTracking::changeJobState( JobEntry::SUCCESS , $this->tracking );
                    
                    $this->unlock( $this->jobName );
                    
                    exit();
                break;
            }

            $job = ( new AttributionAggregatorJob(
                $nextReportType ,
                str_random( 16 ) ,
                $dateRange ,
                $this->modelId ,
                $this->chainOptions
            ) )->onQueue( self::DEFAULT_QUEUE_NAME );

            $this->dispatch( $job );
        }
    }

    protected function logJobException ( JobException $e ) {
        Log::error( str_repeat( '=' , 20 ) );
        Log::error( '' );
        Log::error( str_repeat( '=' , 20 ) );
        Log::error( $e->getMessage() );
        Log::error( $this->jobName );
        Log::error( $e->getFile() );
        Log::error( $e->getLine() );
        Log::error( $e->getTraceAsString() );
    }

    protected function logUncaughtException ( $e ) {
        Log::critical( str_repeat( '=' , 20 ) );
        Log::critical( '' );
        Log::critical( str_repeat( '=' , 20 ) );
        Log::critical( str_repeat( '#' , 20 ) . 'Uncaught Exception' . str_repeat( '#' , 20 ) );
        Log::critical( $this->jobName );
    }

    public function failed() {
        JobTracking::changeJobState( JobEntry::FAILED , $this->tracking );
    }
}
