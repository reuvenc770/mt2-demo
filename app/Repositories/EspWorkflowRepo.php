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

    public function getActiveWorkflowFeeds() {
        return $this->model
                    ->join('esp_workflow_feeds as ewf', 'esp_workflows.id', '=', 'ewf.esp_workflow_id')
                    ->where('status', 1)
                    ->selectRaw('distinct feed_id')
                    ->get();
    }
}