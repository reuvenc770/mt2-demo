<?php

namespace App\Services;
use App\Repositories\EmailActionsRepo;
use App\Repositories\EspWorkflowStepRepo;
use App\Services\MT1SuppressionService;
use App\Services\Interfaces\ISuppressionProcessingStrategy;
use App\Repositories\OfferRepo;

class WorkflowProcessingService {
    
    private $actionsRepo;
    private $suppService;
    private $stepsRepo;
    private $offerRepo;
    private $processingStrategy;

    public function __construct(EmailActionsRepo $actionsRepo, 
        EspWorkflowStepRepo $stepsRepo,
        OfferRepo $offerRepo, 
        MT1SuppressionService $suppService,
        ISuppressionProcessingStrategy $processingStrategy) {

        $this->actionsRepo = $actionsRepo;
        $this->stepsRepo = $stepsRepo;
        $this->offerRepo = $offerRepo;
        $this->suppService = $suppService;
        $this->processingStrategy = $processingStrategy;
    }

    public function process($workflow, $daysBack) {

        $deployIds = $this->stepsRepo->getDeployIds($workflow->id);
        $offerIds = $this->stepsRepo->getOfferIds($workflow->id);
        $this->processingStrategy->setTargetId($workflow->esp_suppression_list);

        $resource = $this->actionsRepo->getEmailsForDeploys($deployIds, $daysBack);

        foreach($resource->cursor() as $emailAddress) {
            // Run these against all suppression lists
            foreach ($offerIds as $offerId) {
                if ($this->suppService->isSuppressed($emailAddress, $offerId)) {
                    // If suppressed, upload to specified list
                    $listId = $this->suppService->getAdvertiserList($offerId);
                    $this->processingStrategy->setTargets([$listId]);
                    $this->processingStrategy->processSuppression($emailAddress);
                }
            }                    

        }
    }

}
