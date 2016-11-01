<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

namespace App\Repositories;

use App\Models\Interfaces\IScheduledFilter;
use DB;
class AttributionScheduleRepo {
    protected $schedule;

    public function __construct ( IScheduledFilter $schedule ) {
        $this->schedule = $schedule;
    }

    public function getRecordsByDate($date){
        return $this->schedule->where("trigger_date", $date);
    }

    public function insertSchedule($emailId, $date){
        return DB::connection("attribution")->statement(
            "INSERT INTO {$this->schedule->getTable()} (email_id, trigger_date)
            VALUES(:id, :trigger_date)
            ON DUPLICATE KEY UPDATE
            email_id = email_id, trigger_date = VALUES(trigger_date)",
            array(
                ':id' => $emailId,
                ':trigger_date' => $date,

            )
        );
    }

    public function bulkDelete($emails){
        return $this->schedule->whereIn("email_id", $emails)->delete();
    }

    public function insertScheduleBulk($emails){
            DB::connection("attribution")->statement(
                "INSERT INTO {$this->schedule->getTable()} (email_id, trigger_date, created_at, updated_at)
            VALUES
                        " . join(' , ', $emails) . "
            ON DUPLICATE KEY UPDATE
            email_id = email_id, trigger_date = VALUES(trigger_date), updated_at = VALUES(updated_at)");
        }
}
