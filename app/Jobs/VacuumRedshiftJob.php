<?php

namespace App\Jobs;

use App\Models\JobEntry;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Facades\JobTracking;
use App\Jobs\Traits\PreventJobOverlapping;

class VacuumRedshiftJob extends Job implements ShouldQueue {
    use InteractsWithQueue, SerializesModels, PreventJobOverlapping;

    private $tracking;
    private $repo;
    private $jobName = 'OptimizeRedshift';

    public function __construct($repo, $tracking) {
        $this->repo = $repo;
        $this->tracking = $tracking;
        JobTracking::startAggregationJob($this->jobName, $this->tracking);
    }

    public function handle() {
        if ($this->jobCanRun($this->jobName)) {
            try {
                $this->createLock($this->jobName);
                JobTracking::changeJobState(JobEntry::RUNNING, $this->tracking);
                
                $this->repo->optimizeDb();

                JobTracking::changeJobState(JobEntry::SUCCESS, $this->tracking);
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
        JobTracking::changeJobState(JobEntry::FAILED, $this->tracking);
    }

}