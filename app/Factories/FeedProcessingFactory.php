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
        $suppression = App::make(MT1SuppressionService::class);
        $suppression->setFeedId($feedId);
        $service->registerSuppression($suppression);

        if (in_array($feedId, [2759, 2798, 2757])) {
            $postingStrategy = App::make(\App\Services\PostingStrategies\AffiliateRoiPostingStrategy::class);
        }
        elseif (in_array($feedId, [2971, 2972, 2987])) {
            $postingStrategy = App::make(\App\Services\PostingStrategies\RmpPostingStrategy::class);
        }
        elseif (2983 === (int)$feedId) {
            $postingStrategy = App::make(\App\Services\PostingStrategies\SimplyJobsPostingStrategy::class);
        }
        else {
            throw new \Exception("$feedId does not have a posting strategy");
        }

        $exportInfo = EspDataExport::where('feed_id', $feedId)->first();
        $workflow = EspWorkflowFeed::where('feed_id', $feedId)->first();

        $espAccount = EspApiAccount::getAccount($exportInfo->esp_account_id);
        $apiService = APIFactory::createApiReportService($espAccount->esp->name, $espAccount->id);

        $reportRepo = App::make(\App\Repositories\FeedDateEmailBreakdownRepo::class);
        $dataRepo = App::make(\App\Repositories\FirstPartyRecordDataRepo::class);
        $logRepo = App::make(\App\Repositories\EspWorkflowLogRepo::class);
        $processingService = new FirstPartyRecordProcessingService($apiService, $reportRepo, $dataRepo, $postingStrategy, $logRepo);

        $suppStrategyName = 'App\\Services\\SuppressionProcessingStrategies\\FirstPartySuppressionProcessingStrategy';
        $suppStrategy = new $suppStrategyName(App::make(\App\Repositories\FirstPartyOnlineSuppressionListRepo::class), $apiService);
        $suppStrategy->setFeedId($feedId);
        $service->setSuppressionProcessingStrategy($suppStrategy);

        $processingService->setFeedId($feedId);
        $processingService->setTargetId($exportInfo->target_list);
        $processingService->setWorkflowId($workflow->esp_workflow_id);
        $service->registerProcessing($processingService);

        return $service;
    }


    private static function setUpThirdPartyService(&$service) {
        // Add suppression
        $service->registerSuppression(App::make(GlobalSuppressionService::class));
        $suppStrategy = App::make(\App\Services\SuppressionProcessingStrategies\ThirdPartySuppressionProcessingStrategy::class);
        $service->setSuppressionProcessingStrategy($suppStrategy);

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
        $service->registerProcessing($processingService);

        return $service;
    }

    public static function createWorkflowProcessingService($workflow) {

        $actionsRepo = App::make(\App\Repositories\EmailActionsRepo::class);
        $offerRepo = App::make(\App\Repositories\OfferRepo::class);
        $stepsRepo = App::make(\App\Repositories\EspWorkflowStepRepo::class);
        $suppService = App::make(\App\Services\MT1SuppressionService::class);

        // get a feed id
        // currently a hack - should redo how these are stored
        // This would be a good place to update actions ... but we don't know how
        $feedId = $workflow->feeds->first()->id;

        $espAccount = EspApiAccount::getAccount($workflow->esp_account_id);
        $apiService = APIFactory::createApiReportService($espAccount->esp->name, $espAccount->id);
        $suppStrategy = new FirstPartySuppressionProcessingStrategy(App::make(\App\Repositories\FirstPartyOnlineSuppressionListRepo::class), $apiService);
        $suppStrategy->setFeedId($feedId);

        return new \App\Services\WorkflowProcessingService($actionsRepo, $stepsRepo, $offerRepo, $suppService, $suppStrategy);
    }

}
