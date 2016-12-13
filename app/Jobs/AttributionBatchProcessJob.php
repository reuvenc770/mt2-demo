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

class AttributionBatchProcessJob extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels, PreventJobOverlapping;

    private $jobName;
    private $data;
    private $modelId;
    private $tracking;
    private $userEmail;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($data, $modelId, $tracking, $userEmail) {
        $this->data = $data;
        $this->modelId = $modelId;
        $this->tracking = $tracking;
        $this->jobName = 'AttributionBatchJob' . $modelId . $tracking;
        $this->userEmail = $userEmail;
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

                $service = ServiceFactory::createAttributionBatchService($this->modelId);       
                $service->process($this->data, $this->modelId, $this->userEmail);

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
