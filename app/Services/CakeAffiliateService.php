<?php
/**
 * @author Adam Chin <achin@zetaglobal.net>
 */

namespace App\Services;

use App\Repositories\CakeAffiliateRepo;

class CakeAffiliateService {
    protected $repo;

    public function __construct ( CakeAffiliateRepo $repo ) {
        $this->repo = $repo;
    }

    public function getAll () {
        return $this->repo->getAll();
    }
}
