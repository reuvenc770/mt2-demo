<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

namespace App\Services\MT1Services;

use App\Repositories\MT1Repositories\DataCleanseRepo;
use App\Services\ServiceTraits\PaginateList;

class DataCleanseService {
    use PaginateList;

    public $repo;

    public function __construct ( DataCleanseRepo $repo ) {
        $this->repo = $repo;
    }

    public function getType () {
        return $this->repo->getType();
    }

    public function getModel () {
        return $this->repo->getModel();
    }
}
