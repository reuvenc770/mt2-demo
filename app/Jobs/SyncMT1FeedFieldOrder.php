<?php
/**
 * @author Adam Chin <achin@zetaglobal.com>
 */

namespace App\Jobs;

use App\Jobs\MonitoredJob;

class SyncMT1FeedFieldOrder extends MonitoredJob
{
    protected $jobName = "SyncMT1FeedFieldOrder";
    protected $tracking;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct( $tracking , $runtimeThreshold="15m" )
    {
        $this->tracking = $tracking;
        $this->jobName .= '-' . $tracking;

        parent::__construct(
            $this->jobName , 
            $runtimeThreshold ,
            $tracking
        );
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handleJob()
    {
        $service = \App::make( \App\Services\FeedService::class );

        $service->syncFileFields();
    }
}
