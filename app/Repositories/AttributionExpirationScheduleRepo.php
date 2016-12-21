<?php

namespace App\Repositories;

use App\Repositories\AttributionScheduleRepo;
use App\Models\AttributionExpirationSchedule;

class AttributionExpirationScheduleRepo extends AttributionScheduleRepo {
    public function __construct ( AttributionExpirationSchedule $schedule ) {
        parent::__construct($schedule);
    }
}
