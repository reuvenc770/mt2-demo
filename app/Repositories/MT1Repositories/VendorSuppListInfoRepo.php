<?php

namespace App\Repositories\MT1Repositories;

use App\Models\MT1Models\VendorSuppListInfo;
use DB;

class VendorSuppListInfoRepo {
    protected $model;

    public function __construct ( VendorSuppListInfo $model ) {
        $this->model = $model;
    }

    public function pullForSync($lookback) {
        return $this->model->where('list_id', '>', 0); // why they liked the idea of id=0 I don't know
    }
}