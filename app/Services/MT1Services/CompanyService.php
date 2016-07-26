<?php

namespace App\Services\MT1Services;

use App\Repositories\MT1Repositories\CompanyInfoRepo;

class CompanyService {
    protected $repo;

    public function __construct ( CompanyInfoRepo $repo ) {
        $this->repo = $repo;
    }

    public function getDeploysForAdvertiser($advertiser) {
        return $this->repo->getDeploysForAdvertiser($advertiser);
    }
}
