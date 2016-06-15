<?php

namespace App\Services;
use App\Services\Interfaces\IEtl;

class AbstractEtlService implements IEtl {

    protected $sourceRepo;
    protected $targetRepo;
    protected $data;

    public function __construct($sourceRepo, $targetRepo) {
        $this->sourceRepo = $sourceRepo;
        $this->targetRepo = $targetRepo;
    }

    public function extract($lookback) {
        $this->data = $this->sourceRepo->pull($lookback);
    }

    public function load() {
        foreach ($this->data as $row) {
            $row = $this->transform($row);
            $this->targetRepo->loadData($row);
        }
    }

    protected function transform($row) {
        return $row;
    }
}