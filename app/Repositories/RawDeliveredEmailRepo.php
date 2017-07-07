<?php

namespace App\Repositories;

use App\Models\RawDeliveredEmail;
use Carbon\Carbon;

class RawDeliveredEmailRepo {

    private $model;
    
    public function __construct(RawDeliveredEmail $model) {
        $this->model = $model;
    }

    public function massInsert($records) {
        $this->model->insert($records);
    }

    public function isValidActionType($recordType) {
        return $recordType === 'deliverable';
    }

    public function pullModelSince($lookBack){
        $date = Carbon::today()->subDay($lookBack)->startOfDay();
        return $this->model->where("created_at", ">=", $date);
    }

    public function clearOutPast($lookback){
        $date = Carbon::today()->subDay($lookback)->startOfDay();
        return $this->model->where("created_at", '<=', $date)->delete();
    }

    public function getMinIdForDate($date) {
        return $this->model->where('created_at', '>=', $date)->min('id');
    }

    public function getMaxIdForDate($date) {
        return $this->model->where('created_at', '>=', $date)->max('id');
    }

    public function getSegmentBetweenIds($startId, $endId) {
        $startId = (int)$startId;
        $endId = (int)$endId;

        if ($startId > 0 && $endId > 0 && $startId < $endId) {
            return $this->model->whereRaw("id BETWEEN $startId AND $endId")->get();
        }
        else {
            return [];
        }
    }
}