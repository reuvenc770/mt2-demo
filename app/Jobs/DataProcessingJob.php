<?php

namespace App\Jobs;

use App\Models\JobEntry;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Facades\JobTracking;
use App\Factories\DataProcessingFactory;
use App\Jobs\Traits\PreventJobOverlapping;

class DataProcessingJob extends Job implements ShouldQueue {
    use InteractsWithQueue, SerializesModels, PreventJobOverlapping;

    private $tracking;

    private $jobName;

    public function __construct($jobName, $tracking) {
        $this->jobName = $jobName;
        $this->tracking = $tracking;
        JobTracking::startAggregationJob($this->jobName, $this->tracking);
    }

    public function handle() {
        if ($this->jobCanRun($this->jobName)) {
            $this->createLock($this->jobName);
            JobTracking::changeJobState(JobEntry::RUNNING,$this->tracking);
            echo "{$this->jobName} running" . PHP_EOL;
            $service = DataProcessingFactory::create($this->jobName);
            $service->run();

            JobTracking::changeJobState(JobEntry::SUCCESS,$this->tracking);
            $this->unlock($this->jobName);
        }
        else {
            echo "Still running {$this->jobName} - job level" . PHP_EOL;
            JobTracking::changeJobState(JobEntry::SKIPPED,$this->tracking);
        }
    }

    public function failed() {
        JobTracking::changeJobState(JobEntry::FAILED,$this->tracking);
        $this->unlock($this->jobName);
    }

}