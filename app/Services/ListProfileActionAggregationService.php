<?php

namespace App\Services;

use App\Services\Interfaces\IEtl;
use App\Repositories\EmailActionsRepo;
use App\Repositories\EmailActionAggregationRepo;
use App\Repositories\CakeConversionRepo;


class ListProfileActionAggregationService implements IEtl {

    private $actionsRepo;
    private $aggregationRepo;
    private $cakeRepo;
    private $resultSet;
    private $conversions;
    
    public function __construct(EmailActionsRepo $actionsRepo, CakeConversionRepo $cakeRepo,  EmailActionAggregationRepo $aggregationRepo) {
        $this->actionsRepo = $actionsRepo;
        $this->aggregationRepo = $aggregationRepo;
        $this->cakeRepo = $cakeRepo;
    }

    public function extract($lookback) {
        $this->resultSet = $this->actionsRepo->pullAggregatedActions($lookback);
        $this->conversions = $this->cakeRepo->getConversionsByEmailId();
    }

    public function load() {
        // Add actions
        $this->aggregationRepo->setType('action');

        $this->resultSet->each(function($data, $key) {
            $this->aggregationRepo->insertBatch($data);
        }, 50000);

        $this->aggregationRepo->cleanUpBatch();

        // Add conversions
        $this->aggregationRepo->setType('conversion');

        $this->conversions->each(function($data, $id) {
            $this->aggregationRepo->insertBatch($data);
        }, 50000);
        
        $this->aggregationRepo->cleanUpBatch();
    }
}


        