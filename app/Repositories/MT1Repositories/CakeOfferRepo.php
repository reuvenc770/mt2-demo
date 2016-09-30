<?php

namespace App\Repositories\MT1Repositories;

use App\Models\MT1Models\CakeOffer;
use DB;

class CakeOfferRepo {
    protected $model;

    public function __construct (CakeOffer $model) {
        $this->model = $model;
    }

    public function pullForSync($lookback) {
        return $this->model;
    }
}
