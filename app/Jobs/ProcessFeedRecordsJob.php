<?php

namespace App\Jobs;

use App\Models\JobEntry;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Facades\JobTracking;
use App\Factories\FeedProcessingFactory;
use App\Jobs\Traits\PreventJobOverlapping;

class ProcessFeedRecordsJob extends Job implements ShouldQueue {
    use InteractsWithQueue, SerializesModels, PreventJobOverlapping;

    private $party;
    private $tracking;
    private $records;
    private $feedId;
    const JOB_NAME_BASE = 'FeedProcessing';
    private $jobName;

    public function __construct($party, $feedId, $records, $tracking) {
        $this->jobName = self::JOB_NAME_BASE . "-$party-$tracking";
        $this->tracking = $tracking;
        $this->records = $records;
        $this->party = $party;
        $this->feedId = $feedId;
        JobTracking::startAggregationJob($this->jobName, $this->tracking);
    }

    public function handle() {
        if ($this->jobCanRun($this->jobName)) {
            try {
                $this->createLock($this->jobName);
                JobTracking::changeJobState(JobEntry::RUNNING,$this->tracking);

                echo "{$this->jobName} running" . PHP_EOL;

                $service = FeedProcessingFactory::createService($this->party, $this->feedId);
                $service->process($this->records);

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
            JobTracking::changeJobState(JobEntry::SKIPPED, $this->tracking);
        }
    }

    public function failed() {
        JobTracking::changeJobState(JobEntry::FAILED,$this->tracking);
    }

}