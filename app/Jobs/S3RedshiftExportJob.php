<?php

namespace App\Jobs;

use App\Models\JobEntry;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Facades\JobTracking;
use App\Jobs\Traits\PreventJobOverlapping;
use App\Factories\ServiceFactory;

class S3RedshiftExportJob extends Job implements ShouldQueue {
    use InteractsWithQueue, SerializesModels, PreventJobOverlapping;

    private $tracking;
    private $entity;
    private $jobName;
    private $getAll;

    public function __construct($entity, $getAll, $tracking) {
        $this->entity = $entity;
        $this->jobName = $entity . '-s3';
        $this->tracking = $tracking;
        $this->getAll = $getAll;
        JobTracking::startAggregationJob($this->jobName, $this->tracking);
    }

    public function handle() {
        if ($this->jobCanRun($this->jobName)) {
            try {
                $this->createLock($this->jobName);
                JobTracking::changeJobState(JobEntry::RUNNING,$this->tracking);
                echo "{$this->jobName} running" . PHP_EOL;

                $service = ServiceFactory::createAwsExportService($this->entity);
                $rows = 0;

                if ($this->getAll) {
                    $rows = $service->extractAll();
                    $service->loadAll();
                }
                else {
                    $rows = $service->extract();
                    $service->load();
                }

                JobTracking::changeJobState(JobEntry::SUCCESS, $this->tracking, $rows);
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
    }

}