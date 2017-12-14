<?php

namespace App\Jobs;

use App\Models\EspWorkflowFeed;
use App\Facades\JobTracking;
use App\Factories\FeedProcessingFactory;

class FirstPartyReprocessingJob extends MonitoredJob {

    protected $tracking;
    private $lookback;
    private $feed;
    protected $jobName;
    const BASE_NAME = 'FirstPartyActionsReprocessing';

    public function __construct($feed, $lookback, $tracking, $runtimeThreshold) {
        $this->feed = $feed;
        $this->lookback = $lookback;
        $this->tracking = $tracking;
        $this->jobName = self::BASE_NAME . '-' . $feed->id;

        parent::__construct($this->jobName,$runtimeThreshold,$tracking);
    }

    public function handleJob() {
        $workflowRelation = EspWorkflowFeed::where('feed_id', $feed->id)->espWorkflow;

        if ($workflowRelation) {
            $workflow = $workflowRelation->first();
        }
        else {
            throw new \Exception("Feed $feedId does not have a workflow");
        }

        $service = FeedProcessingFactory::createWorkflowProcessingService($this->feed, $workflow);
        $service->process($workflow, $this->lookback);
    }
}