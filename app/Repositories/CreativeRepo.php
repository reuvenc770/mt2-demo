<?php

namespace App\Repositories;

use App\Models\Creative;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Query\Builder;

class CreativeRepo {
  
    private $model;

    public function __construct(Creative $model) {
        $this->model = $model;
    } 

    public function updateOrCreate($data) {
        $this->model->updateOrCreate(['id' => $data['id']], $data);
    }

}