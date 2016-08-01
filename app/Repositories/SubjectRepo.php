<?php

namespace App\Repositories;

use App\Models\Subject;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Query\Builder;

class SubjectRepo {
  
    private $model;

    public function __construct(Subject $model) {
        $this->model = $model;
    } 

    public function updateOrCreate($data) {
        $this->model->updateOrCreate(['id' => $data['id']], $data);
    }

}