<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

namespace App\Jobs;

use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Carbon\Carbon;

use App\Factories\ServiceFactory;
use App\Services\CakeConversionService;
use App\Models\JobEntry;
use App\Facades\JobTracking;

class AttributionConversionJob extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels, DispatchesJobs;

    CONST JOB_NAME = "AttributionConversionJob";

    const PROCESS_MODE_SAVE = 'save';
    const PROCESS_MODE_REALTIME = 'realtime';
    const PROCESS_MODE_RERUN = 'rerun';
    const PROCESS_MODE_DATA_ONLY = 'dataOnly';

    protected $processMode;
    protected $dateRange;
    protected $recordType;

    protected $currentDate;
    protected $tracking;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct( $processMode , $recordType , $tracking , $dateRange , $currentDate = null )
    {
        $this->processMode = $processMode;
        $this->recordType = $recordType;
        $this->tracking = $tracking;
        $this->dateRange = $dateRange;
        $this->currentDate = $currentDate;

        $fullJobName = self::JOB_NAME . '-' . $this->processMode . '-' . $recordType . 'Type-' . ( is_null( $this->currentDate ) ? 'initial' : 'current=' . Carbon::parse( $this->currentDate )->addDay()->toDateString() )  . '-range=';

        JobTracking::startTrackingJob( $fullJobName , $this->dateRange[ 'start' ] , $this->dateRange[ 'end' ] , $this->tracking );
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle( CakeConversionService $cakeService )
    {
        JobTracking::changeJobState(JobEntry::RUNNING,$this->tracking);

        do {
            if ( is_null( $this->currentDate ) ) {
                $this->currentDate = $this->dateRange[ 'start' ];
            } elseif ( $this->currentDate == $this->dateRange[ 'end' ] ) {
                JobTracking::changeJobState( JobEntry::SKIPPED , $this->tracking );
                
                return;
            } else {
                $this->currentDate = Carbon::parse( $this->currentDate )->addDay()->toDateString();
            }

            $cakeService->updateConversionsFromAPI( $this->processMode , $this->recordType , $this->currentDate );

            if ( in_array( $this->processMode , [ self::PROCESS_MODE_RERUN , self::PROCESS_MODE_REALTIME ] ) ) {
                $job = new AttributionAggregatorJob(
                    'Record' ,
                    str_random( 16 ) ,
                    [ 'start' => Carbon::parse( $this->currentDate )->startOfDay()->toDateTimeString() , 'end' => Carbon::parse( $this->currentDate )->endOfDay()->toDateTimeString() ] ,
                    null ,
                    [ 'processMode' => $this->processMode , 'dateRange' => $this->dateRange , 'currentDate' => $this->currentDate ]
                );

                $this->dispatch( $job );
            }

            if ( $this->processMode == self::PROCESS_MODE_DATA_ONLY ) {
                $this->dispatch( new AttributionConversionJob(
                    $this->processMode ,
                    'all' ,
                    str_random( 16 ) , 
                    $this->dateRange ,
                    $this->currentDate
                ) );
            }
        } while ( $this->processMode === 'save' && $this->currentDate !== $this->dateRange[ 'end' ] ); 

        JobTracking::changeJobState( JobEntry::SUCCESS , $this->tracking );
    }

    public function failed()
    {
        JobTracking::changeJobState( JobEntry::FAILED , $this->tracking );
    }
}
