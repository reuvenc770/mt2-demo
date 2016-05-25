<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

namespace App\Services\MT1Services;

use App\Repositories\MT1Repositories\OfferCategoryRepo;

class OfferCategoryService {
    protected $repo;

    public function __construct ( OfferCategoryRepo $repo ) {
        $this->repo = $repo;
    }

    public function getAll () {
        return $this->repo->getAll();
    }
}
