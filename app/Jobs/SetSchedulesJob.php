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
use App\Services\EmailFeedActionService;
use App\Jobs\Traits\PreventJobOverlapping;
use App\Services\DeployService;
use App\Repositories\RecordDataRepo;
use App\Repositories\FirstPartyRecordDataRepo;
use Log;

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

    public function handle(AttributionRecordTruthService $truthService, 
        EmailFeedAssignmentService $assignmentService,
        EmailFeedActionService $emailFeedActionService,
        DeployService $deployService,
        RecordDataRepo $recordDataRepo,
        FirstPartyRecordDataRepo $firstPartyRecordDataRepo) {

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
                        $this->handleNewActions($scheduledFilterService, 
                            $truthService, 
                            $emailFeedActionService, 
                            $deployService, 
                            $recordDataRepo, 
                            $firstPartyRecordDataRepo, 
                            $this->emails);
                        
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
        $scheduledFilterService->insertScheduleFilterBulk($emails, 15);
    }


    private function handleNewActions($scheduledFilterService, 
        $truthService, 
        $emailFeedActionService, 
        $deployService, 
        $recordDataRepo, 
        $firstPartyRecordDataRepo, 
        $emails) {


        foreach ($scheduledFilterService->getSetFields() as $field) {
            $truthService->bulkToggleFieldRecord($emails, $field, $scheduledFilterService->getSetFieldValue($field));
        }

        $emailFeedActionService->bulkUpdate($emails);

        // For each email we have a deploy id. Check, if possible, what party that deploy id came from.
        foreach ($emails as $record) {
            if (1 === $deployService->getDeployParty($record['deployId'])) {
                // array of stdClass with prop feed_id
                $feeds = $deployService->getFeedIdsInDeploy($record['deployId']);

                if (count($feeds) === 1) {
                    // The good case
                    $feedId = (int)$feeds[0]->feed_id;
                    $firstPartyRecordDataRepo->setDeliverableStatus($emailId, $feedId, false);
                } 
                elseif (count($feeds) === 0) {
                    // This should not happen - we need to note which deploys use which feeds via list profiles.
                    Log::emergency('First party deploy ' . $record['deployId'] . ' does not have any feeds associated with it.');

                }
                else {
                    // We have an assumption error. A 1st party deploy should only have one feed.
                    Log::emergency('First party deploy ' . $record['deployId'] . ' has multiple feeds: ' . implode(', ', $feeds));
                }
            }
            else {
                // 3 or null (going to assume 3)
                $recordDataRepo->setDeliverableStatus($record['email_id'], false);
            }
        }
    }
}