<?php

namespace App\Services;

use App\Services\ServiceTraits\PaginateList;
use App\Repositories\AttributionModelRepo;
use App\Repositories\AttributionLevelRepo;

class AttributionModelService {
    use PaginateList;

    protected $repo;
    protected $levels;
 
    public function __construct ( AttributionModelRepo $repo , AttributionLevelRepo $levels ) {
        $this->repo = $repo;
        $this->levels = $levels;
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

    public function syncLevelsWithMT1 () {
        return $this->levels->syncLevelsWithMT1();
    }

    public function updateModel ( $currentModelId , $currentModelName , $levels ) {
        return $this->repo->updateModel( $currentModelId , $currentModelName , $levels );
    }

    public function getModelFeeds ( $modelId ) {
        return $this->repo->getModelFeeds( $modelId );
    }

    public function setLive ( $modelId ) {
        return $this->repo->setLive( $modelId );
    }

    public function removeFeed ( $modelId , $feedId ) {
        $this->levels->removeFeed( $modelId , $feedId );
    }
}
