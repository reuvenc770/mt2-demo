<?php

namespace App\Repositories\MT1Repositories;

use App\Models\MT1Models\AdvertiserSubject;
use DB;

class AdvertiserSubjectRepo {
    protected $model;

    public function __construct ( AdvertiserSubject $model ) {
        $this->model = $model;
    }

    public function pullForSync($lookback) {
        DB::connection('mt1_data')->statement('SET SESSION CHARACTER_SET_RESULTS = latin1');
        // Need to ignore many ancient, incompatible subject lines
        return $this->model
                    ->join('advertiser_info as ai', 'advertiser_subject.advertiser_id', '=', 'ai.advertiser_id')
                    ->selectRaw('advertiser_subject.*')
                    ->whereRaw('ai.status = "A" and advertiser_subject.approved_flag = "Y"');
    }
}
