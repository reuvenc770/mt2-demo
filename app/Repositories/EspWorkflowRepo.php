<?php

namespace App\Repositories;

use App\Models\EspWorkflow;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Query\Builder;

class EspWorkflowRepo {
  
    private $model;

    public function __construct(EspWorkflow $model) {
        $this->model = $model;
    } 

    public function getActiveWorkflows() {
        return $this->model->where('status', 1)->get();
    }
}