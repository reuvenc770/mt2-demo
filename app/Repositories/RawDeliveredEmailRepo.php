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
        return $this->model->where("created_at", ">=", $lookBack);
    }

    public function clearOutPast($lookback){
        $date = Carbon::today()->subDay($lookback)->startOfDay();
        return $this->model->where("created_at", '<=', $date)->delete();
    }
}