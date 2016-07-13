<?php

namespace App\Services;

use DB;
use Carbon\Carbon;
use App\Repositories\EmailClientAssignmentRepo;
use App\Repositories\AttributionRecordTruthRepo;
use App\Repositories\AttributionScheduleRepo;
use App\Repositories\EmailClientInstanceRepo;


class AttributionService
{
    const EXPIRATION_DAY_RANGE = 10;
    private $truthRepo;
    private $scheduleRepo;
    private $assignmentRepo;
    private $clientInstanceRepo;

    public function __construct(AttributionRecordTruthRepo $truthRepo, 
                                AttributionScheduleRepo $scheduleRepo, 
                                EmailClientAssignmentRepo $assignmentRepo,
                                EmailClientInstanceRepo $clientInstanceRepo) {

        $this->truthRepo = $truthRepo;
        $this->scheduleRepo = $scheduleRepo;
        $this->assignmentRepo = $assignmentRepo;
        $this->clientInstanceRepo = $clientInstanceRepo;
    }   

    protected function getTransientRecords() {
        return $this->truthRepo->getTransientRecords();
    }

    public function run($records) {
        foreach ($records as $record) {

            $beginDate = $record->capture_date;
            $clientId = (int)$record->client_id;
            $oldClientId = (int)$record->client_id;
            $currentAttrLevel = (int)$record->level;
            $actionDateTime = $record->action_datetime;
            $hasAction = (bool)$record->has_action;
            $actionExpired = $record->action_expired;
            $subsequentImports = 0;

            $potentialReplacements = $this->getPotentialReplacements($record->email_id, $beginDate, $clientId);

            foreach ($potentialReplacements as $repl) {

                if ($this->shouldChangeAttribution($beginDate, $hasAction, $actionExpired, $currentAttrLevel $repl->level)) {
                    $beginDate = $repl->capture_date;
                    $currentAttrLevel = (int)$repl->level;
                    $hasAction = (bool)($repl->capture_date > $actionDateTime);
                    $clientId = (int)$repl->client_id;
                    $subsequentImports = 0;
                }
                else {
                    $subsequentImports++;
                }
            }

            // Only run this once we've found the winner
            if ($oldClientId !== $clientId) {
                $this->changeAttribution($record->email_id, $clientId);
                $this->recordHistory($record->email_id, $oldClientId, $ClientId);
                $this->updateScheduleTable($record->email_id, $beginDate);
                $this->updateTruthTable($record->email_id, $beginDate, $hasAction, $actionExpired, $subsequentImports);
            }
            
        }
    }

    protected function getPotentialReplacements($emailId, $beginDate, $clientId) {
        return $this->clientInstanceRepo->getEmailInstancesAfterDate($emailId, $beginDate, $clientId);
    }

    protected function shouldChangeAttribution($captureDate, $hasAction, $actionExpired, $currentAttrLevel, $testAttrLevel) {

        // needs to be explicitly checked - we don't just have the query to watch this
        if ($this->getExpiringDay->gte($captureDate)) {
            // Older than pre-defined X days ago
            
            if ($hasAction && $actionExpired) {
                return true
            }
            elseif (!$hasAction && $testAttrLevel < $currentAttrLevel) {
                // "less than" here means "has a higher attribution level"
                return true
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