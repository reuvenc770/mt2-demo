<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

namespace App\Jobs;

use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\Repositories\AttributionLevelRepo;
use App\Models\JobEntry;
use App\Facades\JobTracking;

class SyncMT1FeedLevels extends Job implements ShouldQueue
{
    const JOB_NAME = "SyncMT1FeedLevels";

    use InteractsWithQueue, SerializesModels;

    protected $levelRepo;
    protected $tracking;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct( $tracking )
    {
        $this->levelRepo = \App::make( \App\Repositories\AttributionLevelRepo::class );

        $this->tracking = $tracking;

        JobTracking::startAggregationJob( self::JOB_NAME , $this->tracking );
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->levelRepo->syncLevelsWithMT1();

        JobTracking::changeJobState( JobEntry::SUCCESS , $this->tracking );
    }

    public function failed() {
        JobTracking::changeJobState( JobEntry::FAILED , $this->tracking );
    }

}
