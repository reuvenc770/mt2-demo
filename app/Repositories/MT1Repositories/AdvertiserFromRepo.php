<?php

namespace App\Repositories\MT1Repositories;

use App\Models\MT1Models\AdvertiserFrom;
use DB;

class AdvertiserFromRepo {
    protected $model;

    public function __construct ( AdvertiserFrom $model ) {
        $this->model = $model;
    }

    public function pullForSync($lookback) {
        return $this->model
                    ->whereNull('date_approved')
                    ->orWhere('date_approved', '>=', DB::raw("CURDATE() - INTERVAL $lookback DAY"));
    }
}