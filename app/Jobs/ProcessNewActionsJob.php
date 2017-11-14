<?php

namespace App\Jobs;

use App\Jobs\MonitoredJob;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Jobs\Traits\PreventJobOverlapping;
use App\Models\JobEntry;
use App\Facades\JobTracking;

class ProcessNewActionsJob extends MonitoredJob implements ShouldQueue
{

    const BASE_JOB_NAME = 'ProcessNewActionsJob';

    protected $jobName;

    protected $dateRange;
    protected $tracking;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct( $dateRange , $tracking, $runtimeThreshold=null )
    {
        $this->dateRange = $dateRange;
        $this->tracking = $tracking;

        $this->jobName = self::BASE_JOB_NAME . ":" . json_encode( $dateRange );

        parent::__construct($this->jobName,$runtimeThreshold,$tracking);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handleJob()
    {
        $newActions = \App::make('\App\Services\NewActionsService');
        #$newActions->updateFirstPartyEmailStatuses($this->dateRange); // no first party for now
        $newActions->updateThirdPartyEmailStatuses( $this->dateRange );
        $newActions->updateAttributionRecordTruths( $this->dateRange );
    }


}
