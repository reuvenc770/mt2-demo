<?php

namespace App\Jobs;

use App\Models\JobEntry;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Facades\JobTracking;
use App\Factories\DataProcessingFactory;

class PopulateEmailCampaignStats extends Job implements ShouldQueue {
        use InteractsWithQueue, SerializesModels;
        const JOB_NAME = "PopulateEmailCampaignStats";

        private $tracking;
        private $maxAttempts;

        public function __construct($tracking) {
            $this->tracking = $tracking;
            $this->maxAttempts = env('MAX_ATTEMPTS', 3);
        }

        public function handle() {
            JobTracking::startAggregationJob(self::JOB_NAME, $this->tracking);
            $service = DataProcessingFactory::create(self::JOB_NAME);
            $service->run();
            JobTracking::changeJobState(JobEntry::SUCCESS,$this->tracking, $this->attempts());
        }

        public function failed() {
            JobTracking::changeJobState(JobEntry::FAILED,$this->tracking, $this->maxAttempts);
        }
}