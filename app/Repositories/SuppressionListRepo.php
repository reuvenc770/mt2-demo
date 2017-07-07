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

    public function getListForName($name) {
        $obj = $this->model
                    ->where('suppression_list_name', $name)
                    ->where('status', 'A')
                    ->first();

        if ($obj) {
            return $obj->id;
        }
        else {
            return null;
        }
    }

    public function prepareTableForSync() {}

}