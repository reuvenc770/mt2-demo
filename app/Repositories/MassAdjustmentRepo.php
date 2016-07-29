<?php

namespace App\Repositories;

use App\Models\Cake\MassAdjustment;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Query\Builder;

class MassAdjustmentRepo {
    
    private $model;

    public function __construct(MassAdjustment $model) {
        $this->model = $model;
    }

    public function saveMassAdjustment($deployId, $amount, $date) {
        $this->model->updateOrCreate(['']);
    }

    public function getAllAdjustments() {
        return $this->model->get();
    }
}