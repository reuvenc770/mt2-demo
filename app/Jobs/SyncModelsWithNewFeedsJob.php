<?php
/**
 * Adam Chin <achin@zetaglobal.com>
 */

namespace App\Jobs;

use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\Services\AttributionModelService;

use App\Models\JobEntry;
use App\Facades\JobTracking;

class SyncModelsWithNewFeedsJob extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    const JOB_NAME = 'SyncModelsWithNewFeedsJob';

    protected $tracking;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct( $tracking )
    {
        $this->tracking = $tracking;

        JobTracking::startAggregationJob( self::JOB_NAME , $this->tracking );
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle( AttributionModelService $service )
    {
        JobTracking::changeJobState( JobEntry::RUNNING , $this->tracking );

        $service->syncModelsWithNewFeeds();

        JobTracking::changeJobState( JobEntry::SUCCESS , $this->tracking );
    }

    public function failed() {
        JobTracking::changeJobState( JobEntry::FAILED , $this->tracking );
    }
}
