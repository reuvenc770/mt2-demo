<?php

namespace App\Services\MT1Services;

use App\Repositories\MT1Repositories\AdvertiserInfoRepo;

class AdvertiserService {
    protected $repo;

    public function __construct ( AdvertiserInfoRepo $repo ) {
        $this->repo = $repo;
    }

    public function getAll () {
        return $this->repo->getAll();
    }
}
