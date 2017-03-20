<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\EmailAttributableFeedLatestData;
use App\Repositories\EmailFeedAssignmentRepo;
use App\Repositories\AttributionRecordTruthRepo;
use App\Repositories\AttributionExpirationScheduleRepo;
use App\Repositories\EmailFeedInstanceRepo;
use App\Repositories\EmailAttributableFeedLatestDataRepo;
use App\Events\AttributionCompleted;
use Cache;

class AttributionBatchService {
    
    private $today;
    private $truthRepo;
    private $scheduleRepo;
    private $assignmentRepo;
    private $feedInstanceRepo;
    private $latestDataRepo;
    private $keyName = 'AttributionJob';
    const EXPIRATION_DAY_RANGE = 3;

    public function __construct(AttributionRecordTruthRepo $truthRepo, 
                                AttributionExpirationScheduleRepo $scheduleRepo, 
                                EmailFeedAssignmentRepo $assignmentRepo,
                                EmailFeedInstanceRepo $feedInstanceRepo,
                                EmailAttributableFeedLatestDataRepo $latestDataRepo) {

        $this->truthRepo = $truthRepo;
        $this->scheduleRepo = $scheduleRepo;
        $this->assignmentRepo = $assignmentRepo;
        $this->feedInstanceRepo = $feedInstanceRepo;
        $this->latestDataRepo = $latestDataRepo;

        $this->today = Carbon::today();
    }


    public function process($records, $modelId, $userEmail = 'none') {

        $isModelRun = 'none' !== $modelId;

        foreach ($records as $key => $record) {

            $subscribeDate = $record->subscribe_date;
            $feedId = (int)$record->feed_id;
            $oldFeedId = (int)$record->feed_id;
            $currentAttrLevel = (int)$record->level;

            $hasAction = (bool)$record->has_action;
            $actionExpired = $record->action_expired;
            $subsequentImports = 0;

            $potentialReplacements = $this->getPotentialReplacements($record->email_id, $subscribeDate, $feedId);
            $savedReplacement = null;
            
            foreach ($potentialReplacements as $repl) {

                if ($this->shouldChangeAttribution($subscribeDate, $hasAction, $actionExpired, $currentAttrLevel, $repl->level)) {
                    $subscribeDate = $repl->subscribe_date;
                    $currentAttrLevel = (int)$repl->level;
                    $hasAction = 0; // by default must be false - can't switch if an action existed
                    $feedId = (int)$repl->feed_id;
                    $subsequentImports = 0;
                    $actionExpired = 0; // again, can't have an action, so it can't be expired
                    $savedReplacement = $repl;
                }
                else {
                    $subsequentImports++;
                }
            }

            // Only run this once we've found the winner
            if ($oldFeedId !== $feedId) {
                $this->changeAttribution($record->email_id, $feedId, $subscribeDate, $modelId);
                
                if (!$isModelRun) {
                    $this->recordHistory($record->email_id, $oldFeedId, $feedId);
                    $this->updateScheduleTable($record->email_id, $subscribeDate);
                    $this->updateTruthTable($record->email_id, $subscribeDate, $hasAction, $actionExpired, $subsequentImports);
                    $this->updateActionStatus($record->email_id, $oldFeedId, $feedId);
                }
            }

        }
        //TODO emailFeedActionRepo is not set
        $this->emailFeedActionRepo->insertStored();
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


    protected function shouldChangeAttribution($beginDate, $hasAction, $actionExpired, $currentAttrLevel, $testAttrLevel) {
    
        if ($hasAction && $actionExpired) {
            return true;
        }
        elseif (0 === $currentAttrLevel) {
            // no attribution at all, so we need to set it
            return true;
        }
        elseif (!$hasAction 
            && Carbon::parse($beginDate)->addDays(self::EXPIRATION_DAY_RANGE)->lte(Carbon::today()) // Unnecessary for the first import, but needed to check subsequent imports
            && $testAttrLevel < $currentAttrLevel) {
            // "less than" here means "has a higher attribution level"
            return true;
        }
        else {
            return false;
        }
    }

    protected function changeAttribution($emailId, $feedId, $subscribeDate, $modelId) {
        if ( 'none' !== $modelId ) {
            $this->assignmentRepo->setLevelModel( $modelId );
        }

        $this->assignmentRepo->assignFeed($emailId, $feedId, $subscribeDate);
    }

    protected function recordHistory($emailId, $oldFeedId, $newFeedId) {
        $this->assignmentRepo->recordSwap($emailId, $oldFeedId, $newFeedId);
    }

    protected function updateTruthTable($emailId, $subscribeDate, $hasAction, $actionExpired, $subseqs) {
        $addlImports = $subseqs >= 1;
        $recentImport = Carbon::parse($subscribeDate)->addDays(self::EXPIRATION_DAY_RANGE)->gte($this->today);

        $this->truthRepo->setRecord($emailId, $recentImport, $hasAction, $actionExpired, $addlImports);
    }

    protected function updateScheduleTable($emailId, $subscribeDate) {
        // update schedule tables
        $nextDate = Carbon::parse($subscribeDate)
                          ->addDays(self::EXPIRATION_DAY_RANGE)
                          ->format('Y-m-d');

        $this->scheduleRepo->insertSchedule($emailId, $nextDate);
    }

    private function updateRecordsAttributionStatus($emailId, $oldFeedId, $newFeedId) {
        $this->latestDataRepo->setAttributionStatus($emailId, $oldFeedId, EmailAttributableFeedLatestData::LOST_ATTRIBUTION);
        $this->latestDataRepo->setAttributionStatus($emailId, $newFeedId, EmailAttributableFeedLatestData::ATTRIBUTED);
    }

}
