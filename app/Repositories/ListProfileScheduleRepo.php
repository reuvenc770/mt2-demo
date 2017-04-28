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

    public function getListProfilesForToday() {
        $dataDb = config('database.connections.mysql.database');
        return DB::connection('list_profile')->select("SELECT 
                    lps.list_profile_id
                FROM 
                    list_profile_schedules lps
                    LEFT JOIN list_profile_list_profile_combine AS lplpc ON lps.list_profile_id = lplpc.list_profile_id
                    INNER JOIN {$dataDb}.deploys AS d ON lplpc.list_profile_combine_id = d.list_profile_combine_id AND d.send_date = curdate() AND d.party = 3
                
                UNION
                     
                SELECT
                    list_profile_id
                FROM
                    list_profile_schedules
                WHERE
                    (run_daily = 1 AND last_run < curdate())
                    OR (run_daily = 1 AND last_run IS NULL)
                    OR (run_weekly = 1 AND day_of_week = :currentWeekDay1 AND last_run < curdate())
                    OR (run_weekly = 1 AND day_of_week = :currentWeekDay2 AND last_run IS NULL)
                    OR (run_monthly = 1 AND day_of_month = :currentDayNumber1 AND last_run < curdate())
                    OR (run_monthly = 1 AND day_of_month = :currentDayNumber2 AND last_run IS NULL)",
            array(
                'currentWeekDay1' => Carbon::today()->format('l'),
                'currentWeekDay2' => Carbon::today()->format('l'),
                'currentDayNumber1' => Carbon::today()->format('d'),
                'currentDayNumber2' => Carbon::today()->format('d')
            )
        );
    }

    public function updateSuccess($id) {
        $today = Carbon::now();
        $this->model->where('list_profile_id', $id)->update(['last_run' => $today]);
    }
}