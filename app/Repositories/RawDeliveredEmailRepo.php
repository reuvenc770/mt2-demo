<?php

namespace App\Repositories;

use App\Models\RawDeliveredEmail;

class RawDeliveredEmailRepo {

    private $model;
    
    public function __construct(RawDeliveredEmail $model) {
        $this->model = $model;
    }

    public function massInsert($records) {
        $this->model->insert($records);
    }

    public function isValidActionType($recordType) {
        return $recordType === 'deliverable';
    }
}