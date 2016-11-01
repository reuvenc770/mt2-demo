<?php

namespace App\Jobs;

use App\Models\JobEntry;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Facades\JobTracking;
use App\Factories\APIFactory;
use App\Jobs\Traits\PreventJobOverlapping;
use App\Services\ImportMt1EmailsService;

class ImportMt1EmailsJob extends Job implements ShouldQueue {
    use InteractsWithQueue, SerializesModels, PreventJobOverlapping;
    const JOB_NAME = "ImportMt1Emails";

    private $tracking;


    public function __construct($tracking) {
        $this->tracking = $tracking;
        JobTracking::startAggregationJob(self::JOB_NAME, $this->tracking);
    }

    public function handle(ImportMt1EmailsService $service) {
        if ($this->jobCanRun(self::JOB_NAME)) {
            try {
                $this->createLock(self::JOB_NAME);
                JobTracking::changeJobState(JobEntry::RUNNING, $this->tracking);

                $service->run();
                
                JobTracking::changeJobState(JobEntry::SUCCESS, $this->tracking);
                $result = $this->unlock(self::JOB_NAME);
                echo "Successfully removed lock: $result" . PHP_EOL;      
            }
            catch (\Exception $e) {
                echo self::JOB_NAME . " failed with {$e->getMessage()}" . PHP_EOL;
                $this->failed();
            }
            finally {
                $this->unlock(self::JOB_NAME);
            }

        }
        else {
            JobTracking::changeJobState(JobEntry::SKIPPED, $this->tracking);
            echo "Still running " . self::JOB_NAME . " - job level" . PHP_EOL;
        }

    }

    public function failed() {
        JobTracking::changeJobState(JobEntry::FAILED, $this->tracking);
        $this->unlock(self::JOB_NAME);
    }
}