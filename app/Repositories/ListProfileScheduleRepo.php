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
        return DB::connection('list_profile')->select("
SELECT list_profile_id,
       group_concat(Distinct offer_id separator ',') AS offers
FROM   (
       (
                  SELECT     `list_profile_schedules`.`list_profile_id`,
                             d.offer_id
                  FROM       `list_profile_schedules`
                  LEFT JOIN  list_profile_list_profile_combine AS `lplpc`
                  ON         `list_profile_schedules`.`list_profile_id` = `lplpc`.`list_profile_id`
                  INNER JOIN {$dataDb}.`deploys` AS `d`
                  ON         `lplpc`.`list_profile_combine_id` = `d`.`list_profile_combine_id`
                  AND        d.send_date = curdate() )
UNION
         (
                SELECT `list_profile_id`,
                       0 AS offer_id
                FROM   `list_profile_schedules`
                WHERE  (
                              `run_daily` = 1
                       AND    last_run < curdate())
                OR     (
                              `run_daily` = 1
                       AND    last_run IS NULL)
                OR     (
                              `run_weekly` = 1
                       AND    `day_of_week` = :currentWeekDay1
                       AND    last_run < curdate())
                OR     (
                              `run_weekly` = 1
                       AND    `day_of_week` = :currentWeekDay2
                       AND    last_run IS NULL)
                OR     (
                              `run_monthly` = 1
                       AND    `day_of_month` = :currentDayNumber1
                       AND    last_run < curdate())
                OR     (
                              `run_monthly` = 1
                       AND    `day_of_month` = :currentDayNumber2
                       AND    last_run IS NULL))) x
GROUP BY `list_profile_id`
	 ",
            array(
                'currentWeekDay1' => Carbon::today()->format('l'),
                'currentWeekDay2' => Carbon::today()->format('l'),
                'currentDayNumber1' => Carbon::today()->format('d'),
                'currentDayNumber2' => Carbon::today()->format('d')
            )
        );
    }

    public function updateSuccess($id) {
        $today = Carbon::today()->format('Y-m-d');
        $this->model->where('list_profile_id', $id)->update(['last_run' => $today]);
    }
}