<?php

namespace App\Jobs;

use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Exceptions\JobException;

use App\Facades\JobTracking;
use App\Models\JobEntry;

use Log;
use Carbon\Carbon;

class AttributionAggregatorJob extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    private $jobName = 'AttributionAggregator';
    private $tracking;

    private $reportName;
    private $aggregator;
    private $dateRange;
    private $modelId;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct( $reportName , $tracking , array $dateRange = null , $modelId = null )
    {
        $this->jobName .= ":{$reportName}:" . ( is_null( $dateRange ) ? Carbon::today()->toDateString() : $dateRange[ 'start' ] . "-" . $dateRange[ 'end' ] );

        $this->reportName = $reportName;
        $this->tracking = $tracking;
        $this->dateRange = $dateRange;
        $this->modelId = $modelId;

        JobTracking::startAggregationJob( $this->jobName , $this->tracking );
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            JobTracking::changeJobState(JobEntry::RUNNING,$this->tracking);

            $className = "\App\Services\Attribution\\" . $this->reportName . "AggregatorService";

            if ( !class_exists( $className ) ) {
                throw new JobException( "Aggregator Service {$this->reportName} does not exist. Either enter an existing service or make a new one." );
            }

            $this->aggregator = \App::make( $className ); 

            $this->aggregator->buildAndSaveReport( $this->dateRange );

            JobTracking::changeJobState( JobEntry::SUCCESS , $this->tracking );
        } catch ( JobException $e ) {
            $this->logJobException( $e );

            throw $e;
        } catch ( \Exception $e ) {
            $this->logUncaughtException( $e );

            throw $e;
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
