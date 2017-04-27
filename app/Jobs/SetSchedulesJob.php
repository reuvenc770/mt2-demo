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
use App\Services\ThirdPartyEmailStatusService;
use App\Jobs\Traits\PreventJobOverlapping;
use Log;

class SetSchedulesJob extends Job implements ShouldQueue {
    use InteractsWithQueue, SerializesModels, PreventJobOverlapping;

    private $jobName;
    private $emails;
    private $eventType;
    private $tracking;
    const NEXT_FREE_DAY = 3; // to be 15 normally

    public function __construct($jobName, $emails, $eventType, $tracking) {
        $this->jobName = $jobName;
        $this->emails = $emails;
        $this->eventType = $eventType;
        $this->tracking = $tracking;
        JobTracking::startAggregationJob($this->jobName, $this->tracking);
    }

    public function handle(AttributionRecordTruthService $truthService, 
        EmailFeedAssignmentService $assignmentService,
        ThirdPartyEmailStatusService $emailFeedActionService) {

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
                        $this->handleNewActions($scheduledFilterService, $truthService, $emailFeedActionService, $this->emails);
                        
                        break;

                    default:
                        break;
                }

                JobTracking::changeJobState(JobEntry::SUCCESS, $this->tracking);
            }
            catch (\Exception $e) {
                echo "{$this->jobName} failed with {$e->getMessage()}" . PHP_EOL;
                $this->release(5);
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
        $scheduledFilterService->insertScheduleFilterBulk($emails, self::NEXT_FREE_DAY);
    }


    private function handleNewActions($scheduledFilterService, $truthService, $emailFeedActionService, $emails) {
        foreach ($scheduledFilterService->getSetFields() as $field) {
            $truthService->bulkToggleFieldRecord($emails, $field, $scheduledFilterService->getSetFieldValue($field));
        }

        $emailFeedActionService->bulkUpdate($emails);
    }
}