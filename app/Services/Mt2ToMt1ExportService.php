<?php

namespace App\Services;

use App\Services\Interfaces\IEtl;
use App\Repositories\RepoInterfaces\Mt2Export;
use App\Repositories\RepoInterfaces\Mt1Import;
use App\Repositories\EtlPickupRepo;

class Mt2ToMt1ExportService implements IEtl {
    
    private $mt2Repo;
    private $mt1Repo;
    private $pickupRepo;
    private $resource;
    private $processName;
    private $startId;

    public function __construct(Mt2Export $mt2Repo, Mt1Import $mt1Repo, EtlPickupRepo $pickupRepo, $processName) {
        $this->mt2Repo = $mt2Repo;
        $this->mt1Repo = $mt1Repo;
        $this->pickupRepo = $pickupRepo;
        $this->processName = $processName;
    }

    public function extract($lookback) {
        $this->startId = $this->pickupRepo->getLastInsertedForName($this->processName);
        $this->resource = $this->mt2Repo->transformForMt1($this->startId);
    }

    public function load() {
        $id = $this->startId;

        foreach($this->resource->cursor() as $row) {
            $this->mt1Repo->insertToMt1($row->toArray());
            $id = $row['tracking_id'];
        }

        $this->pickupRepo->updatePosition($this->processName, $id);
    }

}