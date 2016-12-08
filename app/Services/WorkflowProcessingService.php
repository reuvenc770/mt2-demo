<?php

namespace App\Services;
use App\Repositories\EmailActionsRepo;
use App\Repositories\EspWorkflowStepRepo;
use App\Services\MT1SuppressionService;

class WorkflowProcessingService {
    
    private $actionsRepo;
    private $suppService;
    private $stepsRepo;
    private $processingStrategy;

    public function __construct(EmailActionsRepo $actionsRepo, 
        EspWorkflowStepRepo $stepsRepo, 
        MT1SuppressionService $suppService,
        ISuppressionProcessingStrategy $processingStrategy) {

        $this->actionsRepo = $actionsRepo;
        $this->stepsRepo = $stepsRepo;
        $this->suppService = $suppService;
        $this->processingStrategy = $processingStrategy;
    }

    public function process($workflow, $daysBack) {

        $deployIds = $this->stepsRepo->getDeployIds($workflow->id);
        $offerIds = $this->stepsRepo->getOfferIds($workflow->id);

        $resource = $this->actionsRepo->getEmailsForDeploys($deployIds, $daysBack);

        foreach($resource->cursor() as $emailAddress) {
            // Run these against all suppression lists
            foreach ($offerIds as $offerId) {
                if ($this->suppService->isSuppressed($emailAddress, $offerId)) {
                    $this->processingStrategy->processSuppression($record);
                }
            }                    

        }
    }
}