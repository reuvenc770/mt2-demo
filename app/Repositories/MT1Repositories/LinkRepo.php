<?php

namespace App\Repositories\MT1Repositories;

use App\Models\MT1Models\Link;
use DB;

class LinkRepo {
    protected $model;

    public function __construct ( Link $model ) {
        $this->model = $model;
    }

    public function pullForSync($lookback) {
        return $this->model
                    ->where('date_added', '>=', DB::raw("CURDATE() - INTERVAL $lookback DAY"));
    }
}