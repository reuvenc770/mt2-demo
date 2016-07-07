<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

namespace App\Repositories;

use App\Models\Interfaces\IScheduledFilter;

class AttributionScheduleRepo {
    protected $schedule;

    public function __construct ( IScheduledFilter $schedule ) {
        $this->schedule = $schedule;
    }

    public function getRecordsByDate($date){
        return $this->schedule->where("trigger_date", $date)->get();
    }

    public function insertSchedule($emailId, $date){
        return $this->schedule->create(["email_id" => $emailId, "trigger_date" => $date]);
    }
}