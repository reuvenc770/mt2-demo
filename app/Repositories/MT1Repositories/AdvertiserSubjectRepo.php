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
        return $this->model;
    }
}
