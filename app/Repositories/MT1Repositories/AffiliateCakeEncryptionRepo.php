<?php

namespace App\Repositories\MT1Repositories;

use App\Models\MT1Models\AffiliateCakeEncryption;
use DB;

class AffiliateCakeEncryptionRepo {
    protected $model;

    public function __construct ( AffiliateCakeEncryption $model ) {
        $this->model = $model;
    }

    public function pullForSync($lookback) {
        return $this->model
                    ->where('lastUpdated', '>=', DB::raw("CURDATE() - INTERVAL $lookback DAY"))
                    ->get();
    }
}