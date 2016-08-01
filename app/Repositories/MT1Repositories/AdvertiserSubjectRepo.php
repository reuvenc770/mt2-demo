<?php

namespace App\Repositories\MT1Repositories;

use App\Models\MT1Models\AdvertiserSubject;

class AdvertiserSubjectRepo {
    protected $model;

    public function __construct ( AdvertiserSubject $model ) {
        $this->model = $model;
    }

    public function pullForSync() {
        return $this->model->get();
    }
}