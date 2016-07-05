<?php

namespace App\Services;

use App\Services\ServiceTraits\PaginateList;
use App\Repositories\AttributionModelRepo;

class AttributionModelService {
    use PaginateList;

    protected $repo;

    public function __construct ( AttributionModelRepo $repo ) {
        $this->repo = $repo;
    }

    public function getModel () {
        return $this->repo->getModel();
    }

    public function create ( $name , $levels = null , $templateModelId = null ) {
        return $this->repo->create( $name , $levels , $templateModelId );
    }
}
