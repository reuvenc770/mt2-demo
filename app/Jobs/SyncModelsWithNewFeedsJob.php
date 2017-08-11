<?php
/**
 * Adam Chin <achin@zetaglobal.com>
 */

namespace App\Jobs;

use App\Services\AttributionModelService;


class SyncModelsWithNewFeedsJob extends MonitoredJob
{

    const JOB_NAME = 'SyncModelsWithNewFeedsJob';

    protected $tracking;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct( $tracking, $runtimeThreshold )
    {
        $this->tracking = $tracking;

        parent::__construct(SELF::JOB_NAME,$runtimeThreshold,$tracking);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handleJob()
    {
        $service = \App::make(\App\Services\AttributionModelService::class);

        $service->syncModelsWithNewFeeds();

    }

}
