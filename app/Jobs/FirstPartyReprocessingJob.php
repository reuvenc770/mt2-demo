<?php

namespace App\Jobs;

use App\Models\JobEntry;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Facades\JobTracking;
use App\Factories\FeedProcessingFactory;
use App\Jobs\Traits\PreventJobOverlapping;

class FirstPartyReprocessingJob extends Job implements ShouldQueue {
    use InteractsWithQueue, SerializesModels, PreventJobOverlapping;

    private $tracking;
    private $lookback;
    private $workflow;
    private $jobName;
    const BASE_NAME = 'FirstPartyActionsReprocessing';

    public function __construct($workflow, $lookback, $tracking) {
        $this->workflow = $workflow;
        $this->lookback = $lookback;
        $this->tracking = $tracking;
        $this->jobName = self::BASE_NAME . '-' . $workflow->name;

        JobTracking::startAggregationJob($this->jobName, $this->tracking);
    }

    public function handle() {
        if ($this->jobCanRun($this->jobName)) {
            try {
                $this->createLock($this->jobName);
                JobTracking::changeJobState(JobEntry::RUNNING,$this->tracking);

                $service = FeedProcessingFactory::createWorkflowProcessingService($this->workflow);
                $service->process($this->workflow, $this->lookback);

                JobTracking::changeJobState(JobEntry::SUCCESS,$this->tracking);
            }
            catch (\Exception $e) {
                echo "{$this->jobName} failed with {$e->getMessage()}" . PHP_EOL;
                $this->failed();
            }
            finally {
                $this->unlock($this->jobName);
            }
        }
        else {
            echo "Still running {$this->jobName} - job level" . PHP_EOL;
            JobTracking::changeJobState(JobEntry::SKIPPED,$this->tracking);
        }
    }

    public function failed() {
        JobTracking::changeJobState(JobEntry::FAILED,$this->tracking);
    }

}