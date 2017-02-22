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

use App\Services\AttributionAggregatorService;

use Log;
use Carbon\Carbon;

class AttributionAggregatorJob extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels, PreventJobOverlapping, DispatchesJobs;

    const JOB_NAME = 'AttributionAggregatorJob';

    private $jobName;
    private $tracking;

    private $mode;
    private $dateRange;
    private $currentDateRange;
    private $offerId;
    private $modelId;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct( $mode , array $dateRange , $tracking , $offerId = null , $modelId = null )
    {
        $this->mode = $mode;
        $this->dateRange = $dateRange;
        $this->tracking = $tracking;
        $this->offerId = $offerId;
        $this->modelId = $modelId;

        if ( !in_array( $this->mode , [ AttributionAggregatorService::RUN_STANDARD , AttributionAggregatorService::RUN_CPM ] ) ) {
            throw new JobException( "{$this->jobName}: {$this->mode} is not a valid mode." );
        }

        $this->jobName = self::JOB_NAME . ":Mode-" . $this->mode . ":Range-" . json_encode( $this->dateRange );

        if ( !is_null( $this->offerId ) ) {
            $this->jobName .= ":OfferID-" . $this->offerId;
        }

        if ( !is_null( $this->modelId ) ) {
            $this->jobName .= ":Model-" . $this->modelId;
        }

        JobTracking::startAggregationJob( $this->jobName , $this->tracking );
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle( AttributionAggregatorService $aggregator )
    {
        if ( $this->jobCanRun( $this->jobName ) ) {
            try {
                $this->createLock($this->jobName);

                JobTracking::changeJobState(JobEntry::RUNNING,$this->tracking);

                do {
                    $this->chunkDateRange();

                    \Log::info( 'aggr job running....' );
                    \Log::info( 'model id:' );
                    \Log::info( $this->modelId );

                    if ( $this->mode === AttributionAggregatorService::RUN_STANDARD ) {
                        $aggregator->standardRun( $this->currentDateRange , $this->modelId );
                    }

                    if ( $this->mode === AttributionAggregatorService::RUN_CPM ) {
                        $aggregator->cpmRun( $this->offerId , $this->currentDateRange , $this->modelId );
                    }
                } while ( $this->currentDateRange[ 'end' ] !== $this->dateRange[ 'end' ] );

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

    protected function chunkDateRange () {
        if ( is_null( $this->currentDateRange ) ) {
            $dateCursor = $this->dateRange[ 'start' ];
        } else {
            $dateCursor = $this->currentDateRange[ 'end' ];
        }

        $daysLeft = Carbon::parse( $dateCursor )
                        ->diffInDays( Carbon::parse( $this->dateRange[ 'end' ] ) , false );

        if ( $daysLeft < 0 ) {
            throw new JobException( "Invalid Date Range: {$dateCursor}::{$this->dateRange[ 'end' ]}" );
        }

        if ( $daysLeft <= 31 ) {
            $this->currentDateRange = [
                'start' => $dateCursor ,
                'end' => $this->dateRange[ 'end' ]
            ];
        }

        if ( $daysLeft > 31 ) {
            $this->currentDateRange = [
                'start' => $dateCursor ,
                'end' => Carbon::parse( $dateCursor )->addDays( 31 )->toDateTimeString()
            ];
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
