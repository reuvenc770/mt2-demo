<?php

namespace App\Repositories;

use App\Models\ListProfileSchedule;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Query\Builder;
use Carbon\Carbon;

/**
 *
 */
class ListProfileScheduleRepo {
  
    private $model;

    public function __construct(ListProfileSchedule $model) {
        $this->model = $model;
    }

    public function insert($row) {
        $this->model->insert($row);
    }

    public function getProfilesForToday() {
        $currentWeekDay = Carbon::today()->format('l');
        $currentDayNumber = Carbon::today()->format('d');

        return $this->model
                    ->where(function ($q) {
                        $q->where('run_daily', 1)
                          ->whereRaw("last_run < CURDATE()");
                    })->orWhere(function($q use ($currentWeekDay)) {
                        $q->where('run_weekly', 1)
                          ->where('day_of_week', $currentWeekDay)
                          ->whereRaw("last_run < CURDATE()");
                    })->orWhere(function($q) use ($currentDayNumber) {
                        $q->where('run_monthly', 1)
                          ->where('day_of_month', $currentDayNumber)
                          ->whereRaw("last_run < CURDATE()");
                    })->get();
    }
}