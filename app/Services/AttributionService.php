<?php

namespace App\Services;

use DB;
use Carbon\Carbon;
use App\Repositories\EmailClientAssignmentRepo;
use App\Repositories\AttributionRecordTruthRepo;


class AttributionService
{
    const EXPIRATION_DAY_RANGE = 10;
    private $truthRepo;
    private $scheduleRepo;
    private $assignmentRepo;

    public function __construct(AttributionRecordTruthRepo $truthRepo, AttributionScheduleRepo $scheduleRepo, EmailClientAssignmentRepo $assignmentRepo) {
        $this->truthRepo = $truthRepo;
        $this->scheduleRepo = $scheduleRepo;
    }   $this->assignmentRepo = $assignmentRepo;

    protected function getTransientRecords() {
        // Some records are exempt from attribution. We should know how that works
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

            $potentialReplacements = $this->service
                                          ->getPotentialReplacements($record->email_id, $beginDate, $clientId);

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

            // Only actually run this once we've found the winner
            if ($oldClientId <> $clientId) {
                $this->changeAttribution($record->email_id, $clientId);
                $this->recordHistory($record->email_id, $oldClientId, $ClientId);
                $this->updateScheduleTable($record->email_id, $beginDate);
                $this->updateTruthTable($record->email_id, $beginDate, $hasAction, $actionExpired, $subsequentImports);
            }
            
        }
    }

    protected function getPotentialReplacements($emailId, $beginDate, $clientId) {
        $attrDb = config('database.connections.mysql.attribution.database');

        $union = DB::table('email_client_instances as eci')
                ->select('client_id', 'level', 'capture_date')
                ->join($attrDb . '.attribution_levels as al', 'eci.client_id', '=', 'al.client_id')
                #->join(CLIENT_FEEDS_TABLE, 'eci.client_feed_id', '=', 'cf.id') -- need to uncomment these when client feeds created
                ->where('capture_date', $beginDate)
                ->where('client_id', '<>', $clientId)
                ->where('email_id', $emailId)
                #->where('cf.level', 3)
                ->orderBy('capture_date')

        $reps = DB::table('email_client_instances as eci')
                ->select('client_id', 'level', 'capture_date')
                ->join($attrDb . '.attribution_levels as al', 'eci.client_id', '=', 'al.client_id')
                #->join(CLIENT_FEEDS_TABLE, 'eci.client_feed_id', '=', 'cf.id') -- see above: placeholder for client feeds
                ->where('capture_date', $beginDate)
                ->where('client_id', '<>', $clientId)
                ->where('email_id', $emailId)
                #->where('cf.level', 3)
                ->orderBy('capture_date')
                ->union($union)
                ->get();

        return $reps;
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