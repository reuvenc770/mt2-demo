<?php

namespace App\Services;

use App\Repositories\EspWorkflowStepRepo;

class EspWorkflowStepService {
    
    private $repo;

    public function __construct(EspWorkflowStepRepo $repo) {
        $this->repo = $repo;
    }

    public function createWorkflowOfferTargetListMap($workflowId) {
        $output = [];

        $data = $this->repo->getWorkflow($workflowId);

        foreach($data as $step) {
            if (!isset($output[$step->offer_id])) {
                $output[$step->offer_id] = $step->esp_suppression_list;
            }
        }

        return $output;
    }

}