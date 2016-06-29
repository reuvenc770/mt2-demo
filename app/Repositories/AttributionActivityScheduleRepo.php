<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

namespace App\Repositories;

use App\Models\AttributionActivitySchedule;

class AttributionActivityScheduleRepo {
    protected $schedule;

    public function __construct ( AttributionActivitySchedule $schedule ) {
        $this->schedule = $schedule;
    }

    public function getInactive ( $date ) {
        #retrieves records that will be set inactive for given date. 
    }

    public function updateActivity ( $date ) {
        #pushes back records inactivity date to the provided date.
    }
}
