<?php

namespace App\Repositories;

use App\Models\EspWorkflowLog;
use Carbon\Carbon;

class EspWorkflowLogRepo {
    
    private $model;

    public function __construct(EspWorkflowLog $model) {
        $this->model = $model;
    }

    public function getDataForDate($list, $date) {
        // 1. total count for date
        // 2. 
        /*
Foodstamps Count
Users Received from Foodstamps: 3450
Users Sent To Foodstamps List - 3413
Dupe - 23
Total Duplicates who have signed up 3X or More - 1
Users Received Month to Date from Foodstamps: 71121
---------------------------------------------------
HealthPlansOfAmerica Counts
Users Received from 0bce03ec00000000000000000000000fe75d: 416
Users Received from 0bce03ec000000000000000000000010cb84: 157
Users Received from 0bce03ec000000000000000000000010cb85: 262
Users Sent To Bronto by Status
    0bce03ec00000000000000000000000fe75d - Created - 353
    0bce03ec00000000000000000000000fe75d - Duplicate - 63
    0bce03ec000000000000000000000010cb84 - Created - 132
    0bce03ec000000000000000000000010cb84 - Duplicate - 25
    0bce03ec000000000000000000000010cb85 - Created - 209
    0bce03ec000000000000000000000010cb85 - Duplicate - 52
    0bce03ec000000000000000000000010cb85 - Other Error - 1
Users Received Month To Date from 0bce03ec00000000000000000000000fe75d: 23559
Users Received Month To Date from 0bce03ec000000000000000000000010cb84: 9818
Users Received Month To Date from 0bce03ec000000000000000000000010cb85: 10070

        */
        return $this->model->
    }

    public function monthToDateCount($list) {
        $monthStart = Carbon::today()->format('Y-m-01 00:00:00');
        return $this->model->where('target_list', $list)->where('created_at', '>=', $monthStart)->count();
    }
}