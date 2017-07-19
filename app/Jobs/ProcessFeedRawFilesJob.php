<?php
/**
 * @author Adam Chin <achin@zetaglobal.com>
 */

namespace App\Jobs;

use App\Jobs\MonitoredJob;
use App\Services\RemoteFeedFileService;

class ProcessFeedRawFilesJob extends MonitoredJob
{
    protected $jobName = 'ProcessFeedRawFilesJob-';
    protected $tracking;
    protected $service;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct( $tracking , $runtimeThreshold="15m" )
    {
        $this->tracking = $tracking;
        $this->jobName .= $tracking;

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
    public function handleJob ()
    {
        $this->service = \App::make( RemoteFeedFileService::class );
        $this->service->processNewFiles();
    }
}
