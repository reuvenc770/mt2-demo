<?php

namespace App\Jobs;

use App\Models\JobEntry;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Facades\JobTracking;
use App\Factories\ServiceFactory;
use App\Services\AttributionRecordTruthService;
use App\Services\EmailFeedAssignmentService;
use App\Jobs\Traits\PreventJobOverlapping;

class SetSchedulesJob extends Job implements ShouldQueue {
    use InteractsWithQueue, SerializesModels, PreventJobOverlapping;

    private $jobName;
    private $emails;
    private $eventType;
    private $tracking;

    public function __construct($jobName, $emails, $eventType, $tracking) {
        $this->jobName = $jobName;
        $this->emails = $emails;
        $this->eventType = $eventType;
        $this->tracking = $tracking;
        JobTracking::startAggregationJob($this->jobName, $this->tracking);
    }

    public function handle(AttributionRecordTruthService $truthService, EmailFeedAssignmentService $assignmentService) {
        if ($this->jobCanRun($this->jobName)) {
            try {
                $this->createLock($this->jobName);
                JobTracking::changeJobState(JobEntry::RUNNING, $this->tracking);
                echo "{$this->jobName} running" . PHP_EOL;

                $scheduledFilterService = ServiceFactory::createFilterService($this->eventType);
                

                switch ($this->eventType) {
                    case ("expiration"):
                        $this->handleNewRecords($scheduledFilterService, $truthService, $assignmentService, $this->emails);
                        break;

                    case ("activity"):
                        $this->handleNewActions($scheduledFilterService, $truthService, $this->emails);
                        break;

                    default:
                        break;
                }

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
            JobTracking::changeJobState(JobEntry::SKIPPED,$this->tracking);
        }
    }


    public function failed() {
        JobTracking::changeJobState(JobEntry::FAILED,$this->tracking);
    }


    private function handleNewRecords($scheduledFilterService, $truthService, $assignmentService, $emails) {
        $truthService->insertBulkRecords($emails);
        $assignmentService->insertBulkRecords($emails);
        $scheduledFilterService->insertScheduleFilterBulk($emails, 10);
    }


    private function handleNewActions($scheduledFilterService, $truthService, $emails) {
        foreach ($scheduledFilterService->getFields() as $field) {
            $truthService->bulkToggleFieldRecord($emails, $field, $scheduledFilterService->getDefaultFieldValue($field));
        }
        
        $scheduledFilterService->insertScheduleFilterBulk($emails, 90);
    }
}