<?php

namespace App\Jobs;

use App\Models\JobEntry;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Facades\JobTracking;
use App\Factories\APIFactory;
use App\Jobs\Traits\PreventJobOverlapping;

class ImportMt1EmailsJob extends Job implements ShouldQueue {
    use InteractsWithQueue, SerializesModels, PreventJobOverlapping;
    const JOB_NAME = "ImportMt1Emails";

    private $tracking;
    private $maxAttempts;

    public function __construct($tracking) {
        $this->tracking = $tracking;
        $this->maxAttempts = env('MAX_ATTEMPTS', 3);
    }

    public function handle() {
        if ($this->jobCanRun(self::JOB_NAME)) {
            $this->createLock(self::JOB_NAME);
            JobTracking::startAggregationJob(self::JOB_NAME, $this->tracking);
            $service = APIFactory::createMt1DataImportService(self::JOB_NAME);
            $service->run();
            JobTracking::changeJobState(JobEntry::SUCCESS,$this->tracking, $this->attempts());
            $this->unlock(self::JOB_NAME);
        }
        else {
            echo "Still running " . self::JOB_NAME . " - job level" . PHP_EOL;
        }

    }

    public function failed() {
        JobTracking::changeJobState(JobEntry::FAILED,$this->tracking, $this->maxAttempts);
        $this->unlock(self::JOB_NAME);
    }
}