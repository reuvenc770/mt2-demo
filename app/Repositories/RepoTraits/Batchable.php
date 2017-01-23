<?php

namespace App\Repositories\RepoTraits;
use DB;

trait Batchable {
    
    private $batchData = [];
    private $batchDataCount = 0;
    private $insertThreshold = 10000;
    private $batchInsertQuery = '';
    
    public function batchInsert($row) {
        if ($this->batchDataCount >= $this->insertThreshold) {

            $this->insertStored();
            $this->batchData = [$this->transformRowToString($row)];
            $this->batchDataCount = 1;
        }
        else {
            $this->batchData[] = $this->transformRowToString($row);
            $this->batchDataCount++;
        }
    }
    
    public function insertStored() {
        if ($this->batchDataCount > 0) {
            $this->batchData = implode(', ', $this->batchData);
            $query = $this->buildBatchedQuery($this->batchData);    
            DB::connection($this->model->getConnectionName())->statement($query);

            $this->batchData = [];
            $this->batchDataCount = 0;
        }
    } 
    
    // These should be overwritten, but can't be made abstract and private ... 
    private function transformRowToString($row) {
        return '()';
    }

    private function buildBatchedQuery(&$batchData) {
        return '';
    }
}