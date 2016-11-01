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
        $dataDb = config('database.connections.mysql.database');
        $currentWeekDay = Carbon::today()->format('l'); // day of week, e.g. "Wednesday"
        $currentDayNumber = Carbon::today()->format('d'); // day number

        $union = $this->model
                    ->select('list_profile_id')
                    ->where(function ($q) {
                        $q->where('run_daily', 1)
                          ->whereRaw("last_run < CURDATE()");
                    })->orWhere(funciton ($q) {
                        $q->where('run_daily', 1)
                          ->whereRaw("last_run IS NULL");
                    })->orWhere(function($q) use ($currentWeekDay) {
                        $q->where('run_weekly', 1)
                          ->where('day_of_week', $currentWeekDay)
                          ->whereRaw("last_run < CURDATE()");
                    })->orWhere(function($q) use ($currentWeekDay) {
                        $q->where('run_weekly', 1)
                          ->where('day_of_week', $currentWeekDay)
                          ->whereRaw("last_run IS NULL");
                    })->orWhere(function($q) use ($currentDayNumber) {
                        $q->where('run_monthly', 1)
                          ->where('day_of_month', $currentDayNumber)
                          ->whereRaw("last_run < CURDATE()");
                    })->orWhere(function($q) use ($currentDayNumber) {
                        $q->where('run_monthly', 1)
                          ->where('day_of_month', $currentDayNumber)
                          ->whereRaw("last_run IS NULL");
                    });

        return $this->model
             ->select('d.list_profile_id')
             ->join("$dataDb.deploys as d", "list_profile_schedules.id", '=', 'd.list_profile_id')
             ->whereRaw("d.send_date = CURDATE()")
             ->union($union)
             ->get();
    }

    public function updateSuccess($id) {
        $today = Carbon::today()->format('Y-m-d');
        $this->model->where('list_profile_id', $id)->update(['last_run' => $today]);
    }
}