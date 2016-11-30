<?php

namespace App\Jobs;

use App\Models\JobEntry;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Facades\JobTracking;
use App\Services\FeedSuppressionService;
use App\Jobs\Traits\PreventJobOverlapping;

class FeedSuppressionJob extends Job implements ShouldQueue {
    use InteractsWithQueue, SerializesModels, PreventJobOverlapping;

    private $tracking;
    private $jobName;
    private $feedId;
    const BASE_JOB_NAME = 'SuppressFeed';

    public function __construct($feedId, $tracking) {
        $this->feedId = $feedId;
        $this->jobName = self::BASE_JOB_NAME . '-' . $feedId;
        $this->tracking = $tracking;
        JobTracking::startAggregationJob($this->jobName, $this->tracking);
    }

    public function handle(FeedSuppressionService $service) {
        if ($this->jobCanRun($this->jobName)) {
            try {
                $this->createLock($this->jobName);
                JobTracking::changeJobState(JobEntry::RUNNING,$this->tracking);
                echo "{$this->jobName} running" . PHP_EOL;

                $service->processForFeedId($this->feedId);

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