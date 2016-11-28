<?php

namespace App\Repositories;

use App\Models\SuppressionList;

class SuppressionListRepo {

    private $model;

    public function __construct(SuppressionList $model) {
        $this->model = $model;
    }

    public function insert($row) {
        return $this->model->insertGetId($row);
    }

    public function updateOrCreate($data) {
        $this->model->updateOrCreate(['id' => $data['id']], $data);
    }

}