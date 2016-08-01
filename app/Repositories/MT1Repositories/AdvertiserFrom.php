<?php

namespace App\Repositories\MT1Repositories;

use App\Models\MT1Models\AdvertiserFrom;

class AdvertiserFromRepo {
    protected $model;

    public function __construct ( AdvertiserFrom $model ) {
        $this->model = $model;
    }

    public function pullForSync() {
        return $this->model->get();
    }
}