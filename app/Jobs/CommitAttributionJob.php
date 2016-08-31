<?php

namespace App\Jobs;

use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Jobs\Traits\PreventJobOverlapping;
use App\Factories\ServiceFactory;
use App\Facades\JobTracking;
use App\Models\JobEntry;
use App\Events\AttributionCompleted;

class CommitAttributionJob extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels, PreventJobOverlapping;

    private $jobName = 'AttributionJob';
    private $tracking;
    private $modelId;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($modelId, $tracking) {
        $this->tracking = $tracking;
        $this->modelId = $modelId;
        JobTracking::startAggregationJob($this->jobName, $this->tracking);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle() {

        if ($this->jobCanRun($this->jobName)) {
            try {
                $this->createLock($this->jobName);
                JobTracking::changeJobState(JobEntry::RUNNING, $this->tracking);
                echo "{$this->jobName} running" . PHP_EOL;

                $service = ServiceFactory::createAttributionService($this->modelId);

                $records = $service->getTransientRecords($this->modelId);
                $service->run($records, $this->modelId);

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
            JobTracking::changeJobState(JobEntry::SKIPPED,$this->tracking);
        }

    }

    public function failed() {
        JobTracking::changeJobState(JobEntry::FAILED,$this->tracking);
        $this->unlock($this->jobName);
    }
}
