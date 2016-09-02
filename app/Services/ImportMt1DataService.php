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
        $this->records = $this->mt1Repo->pullForSync($lookback);
    }

    public function load() {
        $this->records->chunk(5000, function($records) {
            foreach ($this->records as $record) {
                $record = $this->mapStrategy->map($record->toArray());
                $this->mt2Repo->updateOrCreate($record);
            }
        });

    }
}