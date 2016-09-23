<?php

namespace App\Repositories\MT1Repositories;

use App\Models\MT1Models\AdvertiserTracking;
use DB;

class AdvertiserTrackingRepo {
    protected $model;

    public function __construct (AdvertiserTracking $model) {
        $this->model = $model;
    }

    public function pullForSync($lookback) {
        // We want only active offers
        return $this->model
                    ->join('advertiser_info as ai', 'advertiser_tracking.advertiser_id', '=', 'ai.advertiser_id')
                    ->where('ai.status', 'A')
                    ->groupBy('advertiser_tracking.advertiser_id', 'link_num');
    }
}
