<?php
/**
 * @author Adam Chin <achin@zetaglobal.com>
 */

namespace App\Repositories;

use App\Models\NotificationSchedule;
use App\Models\NotificationLog;

class NotificationScheduleRepo {
    protected $schedule;
    protected $log;

    public function __construct ( NotificationSchedule $schedule , NotificationLog $log ) {
        $this->schedule = $schedule;
        $this->log = $log;
    }

    public function getAllActiveNotifications () {
        $result = $this->schedule->where( 'status' , 1 );

        $schedules = collect( [] );

        if ( $result->count() ) {
            $schedules = $result->get();
        }

        return $schedules;
    }
}
