<?php

namespace App\Factories;

use App;
use App\Facades\EspApiAccount;
use App\Factories\APIFactory;

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
use App\Services\SuppressionService;
use App\Services\SuppressionListSuppressionService;

// Processors
use App\Services\FirstPartyRecordProcessingService;
use App\Services\ThirdPartyRecordProcessingService;


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
        $suppression = App::make(SuppressionListSuppressionService::class);
        $suppression->setFeedId($feedId);
        $service->registerSuppression($suppression);

        $config = config("firstpartyprocessing.$feedId");

        if (null === $config) {
            throw new \Exception("No configuration found for feed $feedId");
        }

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


        $espAccount = EspApiAccount::getEspAccountDetailsByName($config['espAccountName']);
        $apiService = APIFactory::createApiReportService($espAccount->esp->name, $espAccount->id);

        $reportRepo = App::make(\App\Repositories\FeedDateEmailBreakdownRepo::class);
        $dataRepo = App::make(\App\Repositories\FirstPartyRecordDataRepo::class);
        $processingService = new FirstPartyRecordProcessingService($apiService, $reportRepo, $dataRepo, $postingStrategy);

        $suppStrategyName = 'App\\Services\\SuppressionProcessingStrategies\\FirstPartySuppressionProcessingStrategy';
        $suppStrategy = $suppStrategyName(App::make(\App\Repositories\FirstPartyOnlineSuppressionListRepo::class), $apiService);
        $suppStrategy->setFeedId($feedId);
        $service->setSuppressionProcessingStrategy($suppStrategy);

        $processingService->setFeedId($feedId);
        $processingService->setTargetId($config['targetId']);
        $service->registerProcessing($processingService);

        return $service;
    }


    private static function setUpThirdPartyService(&$service) {
        // Add suppression
        $service->registerSuppression(App::make(SuppressionService::class));
        $suppStrategy = App::make(\App\Services\SuppressionProcessingStrategies\ThirdPartySuppressionProcessingStrategy::class);
        $service->setSuppressionProcessingStrategy($suppStrategy);

        // Add Attribution to Processing
        $service->registerProcessing(App::make(ThirdPartyRecordProcessingService::class));

        return $service;
    }

}