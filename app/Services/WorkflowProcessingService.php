<?php

namespace App\Services;
use App\Repositories\EmailActionsRepo;
use App\Repositories\EspWorkflowStepRepo;
use App\Services\MT1SuppressionService;
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
        $suppressionLists = [];

        foreach ($offerIds as $offerId) {
            $suppressionLists = array_merge($suppressionLists, $this->offerRepo->getSuppressionListIds($offerId));
        }
        $suppressionLists = array_unique($suppressionLists);

        $resource = $this->actionsRepo->getEmailsForDeploys($deployIds, $daysBack);

        foreach($resource->cursor() as $emailAddress) {
            // Run these against all suppression lists
            foreach ($offerIds as $offerId) {
                if ($this->suppService->isSuppressed($emailAddress, $offerId)) {
                    // If suppressed, upload to all lists
                    $this->processingStrategy->processSuppression($emailAddress, $suppressionLists);
                    break;
                }
            }                    

        }
    }
}
