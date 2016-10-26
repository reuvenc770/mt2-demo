<?php

namespace App\Repositories;

use App\Models\ListProfileBaseTable;
use DB;

class ListProfileBaseTableRepo {

    private $model;

    public function __construct(ListProfileBaseTable $model) {
        $this->model = $model;
    }


    public function insert($row) {
        $this->model->insert($row);
    }
}