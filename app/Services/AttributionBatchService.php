<?php

namespace App\Services;

use Carbon\Carbon;
use App\Repositories\EmailFeedAssignmentRepo;
use App\Repositories\AttributionRecordTruthRepo;
use App\Repositories\AttributionScheduleRepo;
use App\Repositories\EmailFeedInstanceRepo;
use App\Events\AttributionCompleted;
use Cache;

class AttributionBatchService {
    
    private $today;
    private $truthRepo;
    private $scheduleRepo;
    private $assignmentRepo;
    private $feedInstanceRepo;
    private $keyName = 'AttributionJob';
    const EXPIRATION_DAY_RANGE = 10;

    public function __construct(AttributionRecordTruthRepo $truthRepo, 
                                AttributionScheduleRepo $scheduleRepo, 
                                EmailFeedAssignmentRepo $assignmentRepo,
                                EmailFeedInstanceRepo $feedInstanceRepo) {

        $this->truthRepo = $truthRepo;
        $this->scheduleRepo = $scheduleRepo;
        $this->assignmentRepo = $assignmentRepo;
        $this->feedInstanceRepo = $feedInstanceRepo;

        $this->today = Carbon::today();
    }


    public function process($records, $modelId, $userEmail = 'none') {

        $isModelRun = 'none' !== $modelId;

        foreach ($records as $key => $record) {

            $beginDate = $record->capture_date;
            $feedId = (int)$record->feed_id;
            $oldFeedId = (int)$record->feed_id;
            $currentAttrLevel = (int)$record->level;

            $hasAction = (bool)$record->has_action;
            $actionExpired = $record->action_expired;
            $subsequentImports = 0;

            $potentialReplacements = $this->getPotentialReplacements($record->email_id, $beginDate, $feedId);
            
            foreach ($potentialReplacements as $repl) {

                if ($this->shouldChangeAttribution($hasAction, $actionExpired, $currentAttrLevel, $repl->level)) {
                    $beginDate = $repl->capture_date;
                    $currentAttrLevel = (int)$repl->level;
                    $hasAction = 0; // by default must be false - can't switch if an action existed
                    $feedId = (int)$repl->feed_id;
                    $subsequentImports = 0;
                    $actionExpired = 0; // again, can't have an action, so it can't be expired
                }
                else {
                    $subsequentImports++;
                }
            }

            // Only run this once we've found the winner
            if ($oldFeedId !== $feedId) {
                $this->changeAttribution($record->email_id, $feedId, $beginDate, $modelId);
                
                if (!$isModelRun) {
                    $this->recordHistory($record->email_id, $oldFeedId, $feedId);
                    $this->updateScheduleTable($record->email_id, $beginDate);
                    $this->updateTruthTable($record->email_id, $beginDate, $hasAction, $actionExpired, $subsequentImports);
                }
            }

        }
        
        Cache::decrement($this->keyName);

        if (0 === (int)Cache::get($this->keyName)) {
            Cache::forget($this->keyName);  // remove from redis

            if ($isModelRun) {
                // Attribution finished. Return model id and remove from storage
                \Event::fire(new AttributionCompleted($modelId,$userEmail)); // need model id
            }
        }
    }


    protected function getPotentialReplacements($emailId, $beginDate, $feedId) {
        if (0 !== $feedId) {
            // assignment exists
            return $this->feedInstanceRepo->getEmailInstancesAfterDate($emailId, $beginDate, $feedId);
        }
        else {
            // going to create an assignment, so we need to get all instances
            return $this->feedInstanceRepo->getInstances($emailId);
        }
        
    }


    protected function shouldChangeAttribution($hasAction, $actionExpired, $currentAttrLevel, $testAttrLevel) {
    
        if ($hasAction && $actionExpired) {
            return true;
        }
        elseif (0 === $currentAttrLevel) {
            // no attribution at all, so we need to set it
            return true;
        }
        elseif (!$hasAction && $testAttrLevel < $currentAttrLevel) {
            // "less than" here means "has a higher attribution level"
            return true;
        }
        else {
            return false;
        }
    }

    protected function changeAttribution($emailId, $feedId, $captureDate, $modelId) {
        if ( 'none' !== $modelId ) {
            $this->assignmentRepo->setLevelModel( $modelId );
        }

        $this->assignmentRepo->assignFeed($emailId, $feedId, $captureDate);
    }

    protected function recordHistory($emailId, $oldFeedId, $newFeedId) {
        $this->assignmentRepo->recordSwap($emailId, $oldFeedId, $newFeedId);
    }

    protected function updateTruthTable($emailId, $captureDate, $hasAction, $actionExpired, $subseqs) {
        $addlImports = $subseqs >= 1;
        $recentImport = Carbon::parse($captureDate)->addDays(self::EXPIRATION_DAY_RANGE)->gte($this->today);

        $this->truthRepo->setRecord($emailId, $recentImport, $hasAction, $actionExpired, $addlImports);
    }

    protected function updateScheduleTable($emailId, $captureDate) {
        // update schedule tables
        $nextDate = Carbon::parse($captureDate)
                          ->addDays(self::EXPIRATION_DAY_RANGE)
                          ->format('Y-m-d');

        $this->scheduleRepo->insertSchedule($emailId, $nextDate);
    }
}
