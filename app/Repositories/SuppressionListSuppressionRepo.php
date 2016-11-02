<?php

namespace App\Repositories;

use App\Models\SuppressionListSuppression;
use DB;

class SuppressionListSuppressionRepo {

    private $model;

    public function __construct ( SuppressionListSuppression $model ) {
        $this->model = $model;
    }

    public function insert($row) {
        return $this->model->insert($row);
    }

    public function updateOrCreate($data) {
        $this->model->updateOrCreate(['id' => $data['id']], $data);
    }

}