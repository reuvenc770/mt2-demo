<?php

namespace App\Repositories;

use App\Models\EspWorkflowLog;
use Carbon\Carbon;
use DB;

class EspWorkflowLogRepo {
    
    private $model;

    public function __construct(EspWorkflowLog $model) {
        $this->model = $model;
    }

    public function getDataForDate($list, $date) {
        $start = $date . ' 00:00:00';
        $end = $date . ' 23:59:59';

        return $this->model
                    ->select(DB::raw("SUM(IF(times_sent > 1, 1, 0)) as duplicates"), 
                            DB::raw("SUM(IF(times_sent > 1, 1, 0)) as egregious_duplicates"))
                    ->where('target_list', $list)
                    ->whereBetween('created_at', [$start, $end])
                    ->get();
    }

    public function monthToDateCount($list, $date) {
        $monthStart = Carbon::parse($date)->format('Y-m-01 00:00:00');
        return $this->model
                    ->where('target_list', $list)
                    ->whereBetween('created_at', [$monthStart, $date])
                    ->count();
    }

    public function getUnsubscribe($list, $date) {}

    public function getActiveLists($date) {
        $start = $date . ' 00:00:00';
        $end = $date . ' 23:59:59';
        return $this->model
                    ->selectRaw("distinct target_list")
                    ->whereBetween('created_at', [$start, $end])
                    ->get();
    }
}