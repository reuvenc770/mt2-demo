<?php

namespace App\Services;

use DB;
use Carbon\Carbon;
use App\Repositories\EmailClientAssignmentRepo;
use App\Repositories\AttributionRecordTruthRepo;
use App\Repositories\AttributionScheduleRepo;
use App\Repositories\EmailClientInstanceRepo;
use App\Repositories\AttributionLevelRepo;
use App\Repositories\EtlPickupRepo;

class AttributionService
{
    const EXPIRATION_DAY_RANGE = 10;
    private $truthRepo;
    private $scheduleRepo;
    private $assignmentRepo;
    private $clientInstanceRepo;
    private $pickupRepo;
    private $name = 'AttributionJob';

    public function __construct(AttributionRecordTruthRepo $truthRepo, 
                                AttributionScheduleRepo $scheduleRepo, 
                                EmailClientAssignmentRepo $assignmentRepo,
                                EmailClientInstanceRepo $clientInstanceRepo,
                                AttributionLevelRepo $levelRepo,
                                EtlPickupRepo $pickupRepo) {

        $this->truthRepo = $truthRepo;
        $this->scheduleRepo = $scheduleRepo;
        $this->assignmentRepo = $assignmentRepo;
        $this->clientInstanceRepo = $clientInstanceRepo;
        $this->levelRepo = $levelRepo;
        $this->pickupRepo = $pickupRepo;
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

    public function run($records) {

        $currentTimestamp = Carbon::now()->timestamp;

        $records->each(function($record, $key) {

            $beginDate = $record->capture_date;
            $clientId = (int)$record->client_id;
            $oldClientId = (int)$record->client_id;

            // Currently get a 95% decrease in query time by running this separately
            $currentAttrLevel = $this->levelRepo->getLevel($clientId);

            $hasAction = (bool)$record->has_action;
            $actionExpired = $record->action_expired;
            $subsequentImports = 0;

            $potentialReplacements = $this->getPotentialReplacements($record->email_id, $beginDate, $clientId);

            foreach ($potentialReplacements as $repl) {

                if ($this->shouldChangeAttribution($beginDate, $hasAction, $actionExpired, $currentAttrLevel, $repl->level)) {
                    $beginDate = $repl->capture_date;
                    $currentAttrLevel = (int)$repl->level;
                    $hasAction = 0; // by default must be false - can't switch if an action existed
                    $clientId = (int)$repl->client_id;
                    $subsequentImports = 0;
                    $actionExpired = 0; // again, can't have an action, so it can't be expired
                }
                else {
                    $subsequentImports++;
                }
            }

            // Only run this once we've found the winner
            if ($oldClientId !== $clientId) {
                $this->changeAttribution($record->email_id, $clientId, $beginDate);
                $this->recordHistory($record->email_id, $oldClientId, $clientId);
                $this->updateScheduleTable($record->email_id, $beginDate);
                $this->updateTruthTable($record->email_id, $beginDate, $hasAction, $actionExpired, $subsequentImports);
            }
            
        }, 50000);
        
        // This is not the current timestamp anymore, but setting it to the start of the run prevents any gaps
        $this->pickupRepo->updatePosition($this->name, $currentTimestamp);
        
    }

    protected function getPotentialReplacements($emailId, $beginDate, $clientId) {
        return $this->clientInstanceRepo->getEmailInstancesAfterDate($emailId, $beginDate, $clientId);
    }

    protected function shouldChangeAttribution($captureDate, $hasAction, $actionExpired, $currentAttrLevel, $testAttrLevel) {

        // needs to be explicitly checked - we don't just have the query to watch this
        if ($this->getExpiringDay()->gte(Carbon::parse($captureDate))) {
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

    protected function changeAttribution($emailId, $clientId, $captureDate) {
        $this->assignmentRepo->assignClient($emailId, $clientId, $captureDate);
    }

    protected function recordHistory($emailId, $oldClientId, $newClientId) {
        $this->assignmentRepo->recordSwap($emailId, $oldClientId, $newClientId);
    }

    protected function updateTruthTable($emailId, $captureDate, $hasAction, $actionExpired, $subseqs) {
        $addlImports = $subseqs >= 1;
        $recentImport = Carbon::parse($captureDate)->gte($this->getExpiringDay());

        $this->truthRepo->setRecord($emailId, $recentImport, $hasAction, $actionExpired, $addlImports);
    }

    protected function updateScheduleTable($emailId, $captureDate) {
        // update schedule tables
        $nextDate = Carbon::parse($captureDate)
                          ->addDays(self::EXPIRATION_DAY_RANGE)
                          ->format('Y-m-d');

        $this->scheduleRepo->insertSchedule($emailId, $nextDate);
    }

    protected function getExpiringDay() {
        return Carbon::today()->subDays(self::EXPIRATION_DAY_RANGE);
    }
}
