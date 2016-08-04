<?php

namespace App\Services;

use App\Services\Interfaces\IMapStrategy;

class ImportMt1DataService {
    protected $mt1Repo;
    protected $mt2Repo;
    protected $mapStrategy;

    protected $records;

    public function __construct ($mt1Repo, $mt2Repo, IMapStrategy $mapStrategy) {
        $this->mt1Repo = $mt1Repo;
        $this->mt2Repo = $mt2Repo;
        $this->mapStrategy = $mapStrategy;
    }

    public function extract($lookback) {
        $this->records = $this->mt1Repo->pullForSync($lookback)->toArray();
    }

    public function load() {
        foreach ($this->records as $record) {
            $record = $this->mapStrategy->map($record);
            $this->mt2Repo->updateOrCreate($record);
        }
    }
}