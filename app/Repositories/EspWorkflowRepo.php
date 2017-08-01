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

    public function getDisplayModel($options) {
        return $this->model
                    ->join('esp_accounts as eac', 'esp_workflows.esp_account_id', '=', 'eac.id')
                    ->select('esp_workflows.id', 'esp_workflows.name', 'account_name', 'esp_workflows.status', 
                        'esp_workflows.created_at', 'esp_workflows.updated_at');
    }

    public function setStatus($id, $status) {
        $this->model->where('id', $id)->update(['status' => $status]);
    }

    public function getForDisplay($id) {
        return $this->model->find($id);
    }

    public function getFeedsForWorkflow($id) {
        $workflow = $this->model->find($id);
        return isset($workflow) ? $workflow->feeds : [];
    }

    public function getName($id) {
        $find = $this->model->find($id);
        return $find ? $find->name : '';
    }
}