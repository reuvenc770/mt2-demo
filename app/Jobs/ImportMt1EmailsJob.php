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
    const JOB_NAME_BASE = "ImportMt1Emails";

    private $tracking;
    private $modulus;

    public function __construct($modulus, $tracking) {
        $this->modulus = $modulus;
        $this->tracking = $tracking;
        $this->jobName = self::JOB_NAME_BASE . '-' . $modulus;
        JobTracking::startAggregationJob($this->jobName, $this->tracking);
    }

    public function handle(ImportMt1EmailsService $service) {
        if ($this->jobCanRun($this->jobName)) {
            try {
                $this->createLock($this->jobName);
                JobTracking::changeJobState(JobEntry::RUNNING, $this->tracking);

                $service->run($this->modulus);
                
                JobTracking::changeJobState(JobEntry::SUCCESS, $this->tracking);
            }
            catch (\Exception $e) {
                echo $this->jobName . " failed with {$e->getMessage()}" . PHP_EOL;
                $this->failed();
            }
            finally {
                $result = $this->unlock($this->jobName);
                echo "Successfully removed lock: $result" . PHP_EOL;  
            }

        }
        else {
            JobTracking::changeJobState(JobEntry::SKIPPED, $this->tracking);
            echo "Still running " . $this->jobName . " - job level" . PHP_EOL;
        }

    }

    public function failed() {
        JobTracking::changeJobState(JobEntry::FAILED, $this->tracking);
    }
}