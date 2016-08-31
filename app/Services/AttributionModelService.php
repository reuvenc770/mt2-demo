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

    public function getLevel ( $clientId , $modelId = null ) {
        return $this->repo->getLevel( $clientId , $modelId );
    }

    public function levels ( $modelId ) {
        return $this->repo->levels( $modelId );
    }

    public function get ( $modelId ) {
        return $this->repo->get( $modelId );
    }

    public function copyLevels ( $currentModelId , $templateModelId ) {
        return $this->repo->copyLevels( $currentModelId , $templateModelId );
    }

    public function updateModel ( $currentModelId , $currentModelName , $levels ) {
        return $this->repo->updateModel( $currentModelId , $currentModelName , $levels );
    }

    public function getModelClients ( $modelId ) {
        return $this->repo->getModelClients( $modelId );
    }
}
