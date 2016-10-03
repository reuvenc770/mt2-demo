<?php

namespace App\Services;

use App\Services\Interfaces\IEtl;
use App\Repositories\EmailActionsRepo;
use App\Repositories\EmailActionAggregationRepo;

class ListProfileActionAggregationService implements IEtl {

    private $actionsRepo;
    private $aggregationRepo;
    private $resultSet;
    
    public function __construct(EmailActionsRepo $actionsRepo, EmailActionAggregationRepo $aggregationRepo) {
        $this->actionsRepo = $actionsRepo;
        $this->aggregationRepo = $aggregationRepo;
    }

    public function extract($lookback) {
        $this->resultSet = $this->actionsRepo->pullAggregatedActions($lookback);
    }

    public function load() {

        $this->resultSet->each(function($data, $key) {
            $this->aggregationRepo->insertBatch($data);
        });

        $this->aggregationRepo->cleanUpBatch();
    }
}