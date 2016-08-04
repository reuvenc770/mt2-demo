<?php

namespace App\Repositories\MT1Repositories;

use App\Models\MT1Models\Creative;
use DB;

class CreativeRepo {
    protected $model;

    public function __construct ( Creative $model ) {
        $this->model = $model;
    }

    public function pullForSync($lookback) {
        return $this->model
                    ->whereNull('creative_date')
                    ->orWhere('creative_date', '>=', DB::raw("CURDATE() - INTERVAL $lookback DAY"))
                    ->get();
    }
}