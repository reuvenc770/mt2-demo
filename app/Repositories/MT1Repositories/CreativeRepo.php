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
        return $this->model;
    }
}
