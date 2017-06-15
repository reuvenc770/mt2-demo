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
    const EXPIRATION_DAY_RANGE = 15;

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


    public function process($records) {
        foreach ($records as $key => $record) {
            $subscribeDate = $record->subscribe_date;
            $feedId = (int)$record->feed_id;
            $oldFeedId = (int)$record->feed_id;
            $currentAttrLevel = (int)$record->level;
            $hasAction = (bool)$record->has_action;
            $actionExpired = $record->action_expired;
            $subsequentImports = 0;
            $currentInstance = true;

            $potentialReplacements = $this->feedInstanceRepo->getInstances($emailId);
            
            foreach ($potentialReplacements as $repl) {
                if ($currentInstance && $repl->feed_id !== $feedid) {
                    // Currently going to replace with the first instance from a different feed
                    $currentInstance = false;
                    $subscribeDate = $repl->subscribe_date;
                    $currentAttrLevel = (int)$repl->level;
                    $feedId = (int)$repl->feed_id;
                    $subsequentImports = 0;
                }
                else {
                    $subsequentImports++;
                }
            }

            // Only run this once we've found the winner
            if ($oldFeedId !== $feedId) {
                $this->changeAttribution($record->email_id, $feedId, $subscribeDate);
                $this->updateScheduleTable($record->email_id, $subscribeDate);
                $this->updateTruthTable($record->email_id, $subscribeDate, $hasAction, $actionExpired, $subsequentImports);
                $this->updateRecordsAttributionStatus($record->email_id, $oldFeedId, $feedId);
            }

        }
        
        Cache::decrement($this->keyName);

        if (0 === (int)Cache::get($this->keyName)) {
            Cache::forget($this->keyName);  // remove from redis
        }
    }

    protected function changeAttribution($emailId, $feedId, $subscribeDate) {
        $this->assignmentRepo->assignFeed($emailId, $feedId, $subscribeDate);
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
