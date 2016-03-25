<?php

namespace App\Jobs;

use App\Models\JobEntry;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Facades\JobTracking;
use App\Factories\APIFactory;

class ImportMt1EmailsJob extends Job implements ShouldQueue {
    use InteractsWithQueue, SerializesModels;
    const JOB_NAME = "ImportMt1Emails";

    private $tracking;
    private $maxAttempts;

    public function __construct($tracking) {
        $this->tracking = $tracking;
        $this->maxAttempts = env('MAX_ATTEMPTS', 3);
    }

    public function handle() {
        JobTracking::startAggregationJob(self::JOB_NAME, $this->tracking);

        if ($this->attempts() > $this->maxAttempts) {
            $this->release(1);
        }

        $service = APIFactory::createMt1DataImportService();
        $service->run();

        JobTracking::changeJobState(JobEntry::SUCCESS,$this->tracking, $this->attempts());
    }

    public function failed() {
        JobTracking::changeJobState(JobEntry::FAILED,$this->tracking, $this->maxAttempts);
    }
}