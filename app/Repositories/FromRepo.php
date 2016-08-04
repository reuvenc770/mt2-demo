<?php

namespace App\Repositories;

use App\Models\From;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Query\Builder;

class FromRepo {
  
    private $model;

    public function __construct(From $model) {
        $this->model = $model;
    } 

    public function updateOrCreate($data) {
        $this->model->updateOrCreate(['id' => $data['id']], $data);
    }

}