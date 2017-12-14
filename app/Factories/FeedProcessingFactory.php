<?php

namespace App\Factories;

use App;
use App\Facades\EspApiAccount;
use App\Factories\APIFactory;
use App\Factories\ServiceFactory;

// Validators
use App\Services\Validators\AgeValidator;
use App\Services\Validators\CaptureDateValidator;
use App\Services\Validators\CountryAndStateValidator;
use App\Services\Validators\EmailValidator;
use App\Services\Validators\IpValidator;
use App\Services\Validators\GenderValidator;
use App\Services\Validators\PhoneValidator;
use App\Services\Validators\SourceUrlValidator;

// Suppression
use App\Services\GlobalSuppressionService;
use App\Services\MT1SuppressionService;
use App\Services\SuppressionProcessingStrategies\FirstPartySuppressionProcessingStrategy;


// Processors
use App\Services\FirstPartyRecordProcessingService;
use App\Services\ThirdPartyRecordProcessingService;

use App\Models\EspDataExport;
use App\Models\EspWorkflowFeed;


class FeedProcessingFactory
{
    /**
     *  $party: integer (1,2,3)
     *  $feedIds: array of feed ids (used to get correct suppression lists)
     */

    public static function createService($party, $feedId = null) {

        $service = App::make(\App\Services\FeedProcessingService::class);

        // Add validation to the service
        $service->registerValidator(App::make(EmailValidator::class))
            ->registerValidator(App::make(IpValidator::class))
            ->registerValidator(App::make(AgeValidator::class))
            ->registerValidator(App::make(GenderValidator::class))
            ->registerValidator(App::make(SourceUrlValidator::class))
            ->registerValidator(App::make(CaptureDateValidator::class))
            ->registerValidator(App::make(CountryAndStateValidator::class))
            ->registerValidator(App::make(PhoneValidator::class));

        // Set up the rest - suppression, processing
        if (1 === $party && $feedId) {
            // We need a feed id for 1st party
            return self::setUpFirstPartyService($service, $feedId);
        }
        elseif (2 === $party) {
            return;
        }
        elseif (3 === $party && !$feedId) {
            // Third party should be feed-agnostic
            return self::setUpThirdPartyService($service);
        }
        else {
            echo "Invalid party type: $party" . PHP_EOL;
        }
    }

    private static function setUpFirstPartyService(&$service, $feedId) {
        $exportInfo = EspDataExport::where('feed_id', $feedId)->first();
        
        if ($exportInfo){
            $postingStrategy = self::createPostingStrategy($exportInfo);
        }
        else {
            throw new \Exception("$feedId does not have a posting strategy");
        }

        $workflowFeed = EspWorkflowFeed::where('feed_id', $feedId)->first();

        if ($workflowFeed && $workflowFeed->espWorkflow) {
            $workflow = $workflowFeed->espWorkflow;
        }
        else {
            throw new \Exception("$feedId does not have a workflow");
        }

        $espAccount = EspApiAccount::getAccount($exportInfo->esp_account_id);
        $apiService = APIFactory::createApiReportService($espAccount->esp->name, $espAccount->id);

        $reportRepo = App::make(\App\Repositories\FeedDateEmailBreakdownRepo::class);
        $dataRepo = App::make(\App\Repositories\FirstPartyRecordDataRepo::class);
        $logRepo = App::make(\App\Repositories\EspWorkflowLogRepo::class);
        $stepsService = App::make(\App\Services\EspWorkflowStepService::class);
        $processingService = new FirstPartyRecordProcessingService($apiService, $reportRepo, $dataRepo, $postingStrategy, $logRepo, $stepsService);

        $suppStrategyName = 'App\\Services\\SuppressionProcessingStrategies\\FirstPartySuppressionProcessingStrategy';
        $suppStrategy = new $suppStrategyName($apiService, $postingStrategy);
        $processingService->setSuppressionProcessingStrategy($suppStrategy);

        $processingService->setFeedId($feedId);
        $processingService->setTargetId($exportInfo->target_list);
        $processingService->setWorkflowId($workflow->id);

        $espStepsRepo = App::make(\App\Repositories\EspWorkflowStepRepo::class);
        $offerIds = $espStepsRepo->getOfferIds($workflow->id);

        $suppression = App::make(MT1SuppressionService::class);
        $lists = $suppression->getSuppressionLists($offerIds);
        $suppression->setOffersWithTypes($lists);
        $processingService->registerSuppression($suppression);

        $service->registerProcessing($processingService);

        return $service;
    }


    private static function setUpThirdPartyService(&$service) {
        // Add Attribution to Processing
        $eventType = 'expiration';
        $filterService = ServiceFactory::createFilterService($eventType);
        $processingService = new ThirdPartyRecordProcessingService(
            App::make(\App\Repositories\EmailRepo::class),
            App::make(\App\Repositories\AttributionLevelRepo::class),
            App::make(\App\Repositories\FeedDateEmailBreakdownRepo::class),
            App::make(\App\Repositories\ThirdPartyEmailStatusRepo::class),
            App::make(\App\Repositories\EmailAttributableFeedLatestDataRepo::class),
            $filterService,
            App::make(\App\Services\EmailFeedAssignmentService::class),
            App::make(\App\Services\AttributionRecordTruthService::class)
        );

        // Add suppression
        $processingService->registerSuppression(App::make(GlobalSuppressionService::class));
        $suppStrategy = App::make(\App\Services\SuppressionProcessingStrategies\ThirdPartySuppressionProcessingStrategy::class);
        $processingService->setSuppressionProcessingStrategy($suppStrategy);

        $service->registerProcessing($processingService);

        return $service;
    }

    public static function createWorkflowProcessingService($feed, $workflow) {
        $actionsRepo = App::make(\App\Repositories\EmailActionsRepo::class);
        $offerRepo = App::make(\App\Repositories\OfferRepo::class);
        $stepsRepo = App::make(\App\Repositories\EspWorkflowStepRepo::class);

        $offerIds = $stepsRepo->getOfferIds($workflow->id);

        $suppression = App::make(MT1SuppressionService::class);
        $lists = $suppression->getSuppressionLists($offerIds);
        $suppression->setOffersWithTypes($lists);

        $espAccount = EspApiAccount::getAccount($workflow->esp_account_id);
        $apiService = APIFactory::createApiReportService($espAccount->esp->name, $espAccount->id);
        $exportInfo = EspDataExport::where('feed_id', $feed->id)->first();

        if ($exportInfo){
            $postingStrategy = self::createPostingStrategy($exportInfo);
        }
        else {
            throw new \Exception("$feedId does not have a posting strategy");
        }

        $suppStrategy = new FirstPartySuppressionProcessingStrategy($apiService, $postingStrategy);

        return new \App\Services\WorkflowProcessingService($actionsRepo, $stepsRepo, $offerRepo, $suppService, $suppStrategy);
    }


    private static function createPostingStrategy(EspDataExport $exportInfo) {
        $className = $exportInfo->posting_class_name;

        if(class_exists("\\App\Services\\PostingStrategies\\{$className}PostingStrategy")) {
            $postingStrategy = App::make("\\App\\Services\\PostingStrategies\\{$className}PostingStrategy");
        }
        else {
            throw new \Exception("$feedId does not have a valid posting strategy");
        }
    }

}
