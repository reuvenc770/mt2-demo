<?php

namespace App\Repositories;

use App\Models\EspWorkflowStep;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Query\Builder;

class EspWorkflowStepRepo {
  
    private $model;

    public function __construct(EspWorkflowStep $model) {
        $this->model = $model;
    }

    public function getStepsForWorkflow($workflowId) {
        return $this->model->where('esp_workflow_id', $workflowId)->orderBy('step', 'asc')->get();
    }

    public function getDeployIds($workflowId) {
        $result = $this->model
            ->select('deploy_id')
            ->where('esp_workflow_id', $workflowId)
            ->get()
            ->toArray();

        return $this->filter($result, 'deploy_id');
    }

    public function getOfferIds($workFlowId) {
        // Need to group by b/c offers can be duplicated
        $result = $this->model
            ->select('offer_id')
            ->where('esp_workflow_id', $workFlowId)
            ->groupBy('offer_id')
            ->get()
            ->toArray();

        return $this->filter($result, 'offer_id');
    }

    private function filter($result, $field) {
        $output = [];

        foreach ($result as $arr) {
            $output[] = $arr[$field];
        }

        return $output;
    }

    public function getWorkflow($id) {
        return $this->model->where('esp_workflow_id', $id)->get();
    }

    public function getEspSuppressionList($workflowId, $offerId) {
        return $this->model
                    ->where('esp_workflow_id', $workflowId)
                    ->where('offer_id', $offerId)
                    ->selectRaw('distinct esp_suppression_list')
                    ->first()
                    ->esp_suppression_list;
    }
}