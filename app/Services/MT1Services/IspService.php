<?php

namespace App\Services\MT1Services;

use App\Repositories\MT1Repositories\IspRepo;

class IspService {
    protected $ispRepo;

    public function __construct ( IspRepo $repo ) {
        $this->ispRepo = $repo;
    }

    public function getAll () {
        return $this->ispRepo->getAll();
    }
}
