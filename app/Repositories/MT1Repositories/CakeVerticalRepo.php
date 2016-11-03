<?php

namespace App\Repositories\MT1Repositories;

use App\Models\MT1Models\CakeVertical;
use DB;

class CakeVerticalRepo {
    protected $model;

    public function __construct (CakeVertical $model) {
        $this->model = $model;
    }

    public function pullForSync($lookback) {
        return $this->model->whereRaw('verticalID < 0');
    }
}
