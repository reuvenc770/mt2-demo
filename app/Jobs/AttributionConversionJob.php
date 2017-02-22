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
use App\Jobs\AttributionAggregatorJob;
use App\Services\AttributionAggregatorService;
use App\Models\JobEntry;
use App\Facades\JobTracking;

class AttributionConversionJob extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels, DispatchesJobs;

    CONST JOB_NAME = "AttributionConversionJob";
    CONST RECORD_TYPE = 'all';

    protected $dateRange;
    protected $currentDate;
    protected $tracking;
    protected $modelId;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct( $tracking , $dateRange , $modelId = null )
    {
        $this->tracking = $tracking;
        $this->dateRange = $dateRange;
        $this->modelId = $modelId;

        $fullJobName = self::JOB_NAME . '-range=';

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
            } else {
                $this->currentDate = Carbon::parse( $this->currentDate )->addDay()->toDateString();
            }

            $cakeService->updateConversionsFromAPI( $this->currentDate );
        } while ( $this->currentDate !== $this->dateRange[ 'end' ] ); 

        \Log::info( 'conv job running..' );
        \Log::info( 'model id:' );
        \Log::info( $this->modelId );
        
        $this->dispatch( new AttributionAggregatorJob(
            AttributionAggregatorService::RUN_STANDARD ,
            $this->dateRange ,
            str_random( 16 ) , 
            null ,
            $this->modelId
        ) );

        JobTracking::changeJobState( JobEntry::SUCCESS , $this->tracking );
    }

    public function failed()
    {
        JobTracking::changeJobState( JobEntry::FAILED , $this->tracking );
    }
}
