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
        return $this->model;
    }
}
