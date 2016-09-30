<?php

namespace App\Repositories\MT1Repositories;

use App\Models\MT1Models\CakeCreativeOfferJoin;
use DB;

class CakeCreativeOfferJoinRepo {
    protected $model;

    public function __construct (CakeCreativeOfferJoin $model) {
        $this->model = $model;
    }

    public function pullForSync($lookback) {
        // We want only active offers
        return $this->model->join('advertiser_info as ai', 'CakeCreativeOfferJoin.creativeID', '=', 'ai.cake_creativeID');
    }
}
