<?php

namespace App\Services\MT1Services;

use App\Repositories\MT1Repositories\AdvertiserRepo;

class AdvertiserService {
    protected $repo;

    public function __construct ( AdvertiserRepo $repo ) {
        $this->repo = $repo;
    }

    public function getAll () {
        return $this->repo->getAll();
    }
}