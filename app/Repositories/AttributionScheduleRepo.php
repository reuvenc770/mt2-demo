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
        return $this->schedule->where("trigger_date", $date)->get();
    }

    public function insertSchedule($emailId, $date){
        return DB::connection("attribution")->statement(
            "INSERT INTO {$this->schedule->getTable()} (email_id, trigger_date)
            VALUES(:id, :trigger_date)
            ON DUPLICATE KEY UPDATE
            email_id = email_id, trigger_date = trigger_date ",
            array(
                ':id' => $emailId,
                ':trigger_date' => $date,

            )
        );
    }


    public function insertScheduleBulk($emails){
        foreach($emails->chunk(10000) as $chunk) {
            DB::connection("attribution")->statement(
                "INSERT INTO {$this->schedule->getTable()} (email_id, trigger_date)
            VALUES
                        " . join(' , ', $chunk) . "
            ON DUPLICATE KEY UPDATE
            email_id = email_id, trigger_date = trigger_date ");
        }
    }
}
