<?php

namespace App\Repositories;

use App\Models\TempStoredEmail;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Query\Builder;

/**
 *
 */
class TempStoredEmailRepo {

    private $emailModel;
    private $batchData = [];
    private $batchDataCount = 0;
    const INSERT_THRESHOLD = 2500;

    public function __construct(TempStoredEmail $emailModel) {
        $this->emailModel = $emailModel;
    }

    public function insert($data) {
        $this->emailModel->insert($data);
    }

    public function batchInsert($row) {
        if ($this->batchDataCount >= self::INSERT_THRESHOLD) {

            $this->insertStored();
            $this->batchData = [$row];
            $this->batchDataCount = 1;
        }
        else {
            $this->batchData[] = $row;
            $this->batchDataCount++;
        }
    }

    public function insertStored() {
        $this->emailModel->insert($this->batchData);
        $this->batchData = [];
        $this->batchDataCount = 0;
    }

}