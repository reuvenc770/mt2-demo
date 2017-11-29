<?php

namespace App\Jobs;

use App\Jobs\Job;

use App\Models\JobEntry;
use App\Facades\JobTracking;
use App\Services\EmailFeedInstanceService;

class UpdateFeedCountJob extends MonitoredJob
{

    protected $jobName = 'UpdateFeedCountJob';
    protected $startDate;
    protected $endDate;
    protected $tracking;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct( $startDate , $endDate , $tracking, $runtimeThreshold )
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        parent::__construct($this->jobName, $runtimeThreshold, $tracking);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handleJob()
    {
        $service = \App::make(\App\Services\EmailFeedInstanceService::class);

        return $service->updateSourceUrlCounts( $this->startDate , $this->endDate );
    }


}
