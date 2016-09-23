<?php

namespace App\Repositories\MT1Repositories;

use App\Models\MT1Models\BrandTemplate;
use DB;

class BrandTemplateRepo {
    protected $model;

    public function __construct (BrandTemplate $model) {
        $this->model = $model;
    }

    public function pullForSync($lookback) {
        return $this->model->where('status', 'A');
    }
}
