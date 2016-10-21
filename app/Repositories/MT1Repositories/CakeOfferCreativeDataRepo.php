<?php

namespace App\Repositories\MT1Repositories;

use App\Models\MT1Models\CakeOfferCreativeData;
use DB;

class CakeOfferCreativeDataRepo {
    protected $model;

    public function __construct (CakeOfferCreativeData $model) {
        $this->model = $model;
    }

    public function pullForSync($lookback) {
        // We want only active offers
        return $this->model->join('advertiser_info as ai', 'CakeOfferCreativeData.creative_id', '=', 'ai.cake_creativeID');
    }
}
