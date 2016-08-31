<?php

namespace App\Services;

use DB;
use Carbon\Carbon;
use App\Repositories\EmailFeedAssignmentRepo;
use App\Repositories\AttributionRecordTruthRepo;
use App\Repositories\AttributionScheduleRepo;
use App\Repositories\EmailFeedInstanceRepo;
use App\Repositories\AttributionLevelRepo;
use App\Repositories\EtlPickupRepo;

class AttributionService
{
    const EXPIRATION_DAY_RANGE = 10;
    private $truthRepo;
    private $scheduleRepo;
    private $assignmentRepo;
    private $feedInstanceRepo;
    private $pickupRepo;
    private $expiringDay;
    private $name = 'AttributionJob';

    public function __construct(AttributionRecordTruthRepo $truthRepo, 
                                AttributionScheduleRepo $scheduleRepo, 
                                EmailFeedAssignmentRepo $assignmentRepo,
                                EmailFeedInstanceRepo $feedInstanceRepo,
                                AttributionLevelRepo $levelRepo,
                                EtlPickupRepo $pickupRepo) {

        $this->truthRepo = $truthRepo;
        $this->scheduleRepo = $scheduleRepo;
        $this->assignmentRepo = $assignmentRepo;
        $this->feedInstanceRepo = $feedInstanceRepo;
        $this->levelRepo = $levelRepo;
        $this->pickupRepo = $pickupRepo;

        $this->expiringDay = Carbon::today()->subDays(self::EXPIRATION_DAY_RANGE);
    }   

    public function getTransientRecords($model) {

        $timestamp = $this->pickupRepo->getLastInsertedForName($this->name);
        $carbonDate = Carbon::createFromTimestamp($timestamp);

        // Checking whether attribution levels have changed since the last run
        $lastAttrLevelChange = Carbon::parse($this->levelRepo->getLastUpdate());

        if ('none' !== $model || $lastAttrLevelChange->gte($carbonDate)) {
            // If a model is specified, or if attribution has changed recently,
            // execute the full run
            return $this->truthRepo->getFullTransients();
        }
        else {
            // Otherwise, run the optimized subset
            $datetime = $carbonDate->toDateTimeString();
            return $this->truthRepo->getOptimizedTransients($datetime);
        }
        
    }

    public function run( $records , $isModelRun = false ) {

        $currentTimestamp = Carbon::now()->timestamp;

        $records->each(function($record, $key) use ( $isModelRun ) {

            $beginDate = $record->capture_date;
            $feedId = (int)$record->feed_id;
            $oldFeedId = (int)$record->feed_id;

            // running this separately currently improves execution time
            $currentAttrLevel = $this->levelRepo->getLevel($feedId);

            $hasAction = (bool)$record->has_action;
            $actionExpired = $record->action_expired;
            $subsequentImports = 0;

            $potentialReplacements = $this->getPotentialReplacements($record->email_id, $beginDate, $feedId);

            foreach ($potentialReplacements as $repl) {

                if ($this->shouldChangeAttribution($beginDate, $hasAction, $actionExpired, $currentAttrLevel, $repl->level)) {
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
                $this->changeAttribution($record->email_id, $feedId, $beginDate);
		
		if ($isModelRun === false) {
		    $this->recordHistory($record->email_id, $oldFeedId, $feedId);
		    $this->updateScheduleTable($record->email_id, $beginDate);
		    $this->updateTruthTable($record->email_id, $beginDate, $hasAction, $actionExpired, $subsequentImports);
                }
            }
            
        }, 50000);
        
        // This is not the current timestamp anymore, but setting it to the start of the run prevents any gaps
        $this->pickupRepo->updatePosition($this->name, $currentTimestamp);
        
    }

    protected function getPotentialReplacements($emailId, $beginDate, $feedId) {
        return $this->feedInstanceRepo->getEmailInstancesAfterDate($emailId, $beginDate, $feedId);
    }

    protected function shouldChangeAttribution($captureDate, $hasAction, $actionExpired, $currentAttrLevel, $testAttrLevel) {

        // needs to be explicitly checked - we don't just have the query to watch this
        if ($this->expiringDay->gte(Carbon::parse($captureDate))) {
            // Older than pre-defined X days ago
            
            if ($hasAction && $actionExpired) {
                return true;
            }
            elseif (!$hasAction && $testAttrLevel < $currentAttrLevel) {
                // "less than" here means "has a higher attribution level"
                return true;
            }
        }

        return false;
    }

    protected function changeAttribution($emailId, $feedId, $captureDate) {
        $this->assignmentRepo->assignFeed($emailId, $feedId, $captureDate);
    }

    protected function recordHistory($emailId, $oldFeedId, $newFeedId) {
        $this->assignmentRepo->recordSwap($emailId, $oldFeedId, $newFeedId);
    }

    protected function updateTruthTable($emailId, $captureDate, $hasAction, $actionExpired, $subseqs) {
        $addlImports = $subseqs >= 1;
        $recentImport = Carbon::parse($captureDate)->gte($this->expiringDay);

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
