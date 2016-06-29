<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

namespace App\Repositories;

use App\Models\AttributionExpirationSchedule;

class AttributionExpirationScheduleRepo {
    protected $schedule;

    public function __construct ( AttributionExpirationSchedule $schedule ) {
        $this->schedule = $schedule;
    }

    public function getExpired ( $date ) {
        #returns expired email records
    }

    public function updateExpiration ( $emailId , $newExpiration ) {
        #updated email record w/ new data for expiration
    }
}
