<?php

namespace App\Jobs;

use App\Models\JobEntry;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Facades\JobTracking;
use App\Factories\DataProcessingFactory;
use App\Repositories\EtlPickupRepo;

class PopulateEmailCampaignStats extends Job implements ShouldQueue {
        use InteractsWithQueue, SerializesModels;
        const JOB_NAME = "PopulateEmailCampaignStats";

        private $lookback;
        private $tracking;
        private $maxAttempts;
        private $etlPickupRepo;

        public function __construct(EtlPickupRepo $etlPickupRepo, $lookback, $tracking) {
            $this->lookback = $lookback;
            $this->tracking = $tracking;
            $this->etlPickupRepo = $etlPickupRepo;

            $this->maxAttempts = env('MAX_ATTEMPTS', 3);
        }

        public function handle() {
            JobTracking::startAggregationJob(self::JOB_NAME, $this->tracking);

            if ($this->attempts() > $this->maxAttempts) {
                $this->release(1);
            }

            $service = DataProcessingFactory::create(self::JOB_NAME, $this->lookback);
            $lastId = $service->run();
            $this->etlPickupRepo->updatePosition(self::JOB_NAME, $lastId);
            JobTracking::changeJobState(JobEntry::SUCCESS,$this->tracking, $this->attempts());
        }

        public function failed() {
            JobTracking::changeJobState(JobEntry::FAILED,$this->tracking, $this->maxAttempts);
        }
}