<?php

namespace App\Jobs;

use App\Models\JobEntry;
use App\Facades\JobTracking;
use App\Factories\FeedProcessingFactory;

class FirstPartyReprocessingJob extends MonitoredJob {

    protected $tracking;
    private $lookback;
    private $workflow;
    protected $jobName;
    const BASE_NAME = 'FirstPartyActionsReprocessing';

    public function __construct($workflow, $lookback, $tracking, $runtimeThreshold) {
        $this->workflow = $workflow;
        $this->lookback = $lookback;
        $this->tracking = $tracking;
        $this->jobName = self::BASE_NAME . '-' . $workflow->name;

        parent::__construct($this->jobName,$runtimeThreshold,$tracking);
        #JobTracking::startAggregationJob($this->jobName, $this->tracking);
    }

    public function handleJob() {
                $service = FeedProcessingFactory::createWorkflowProcessingService($this->workflow);
                $service->process($this->workflow, $this->lookback);
    }
}