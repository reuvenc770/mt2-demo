<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

namespace App\Services\MT1Services;

use App\Repositories\MT1Repositories\CountryRepo;

class CountryService {
    protected $repo;

    public function __construct ( CountryRepo $repo ) {
        $this->repo = $repo;
    }

    public function getAll () {
        return $this->repo->getAll();
    }
}
