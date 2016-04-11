<?php

namespace App\Jobs;

use App\Models\JobEntry;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Facades\JobTracking;
use App\Factories\DataProcessingFactory;

class DataProcessingJob extends Job implements ShouldQueue {
        use InteractsWithQueue, SerializesModels;

        private $tracking;
        private $maxAttempts;
        private $jobName;

        public function __construct($jobName, $tracking) {
            $this->jobName = $jobName;
            $this->tracking = $tracking;
            $this->maxAttempts = env('MAX_ATTEMPTS', 3);
        }

        public function handle() {
            JobTracking::startAggregationJob($this->jobName, $this->tracking);

            $service = DataProcessingFactory::create($this->jobName);
            $service->run();

            JobTracking::changeJobState(JobEntry::SUCCESS,$this->tracking, $this->attempts());
        }

        public function failed() {
            JobTracking::changeJobState(JobEntry::FAILED,$this->tracking, $this->maxAttempts);
        }
}