<?php

namespace App\Factories;

use App;
use App\Repositories\EspApiAccountRepo;
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
        $espAccount = EspApiAccountRepo::getEspInfoByAccountName($config['espAccountName']);

        $apiService = APIFactory::createApi($espAccount->esp->name, $espAccount->id);
        $processingService = new FirstPartyRecordProcessingService($apiService);

        $processingService->setFeedId($feedId);
        $processingService->setTargetId($config['targetId']);
        $service->registerProcessing($processingService);

        return $service;
    }


    private static function setUpThirdPartyService(&$service) {
        // Add suppression
        $service->registerSuppression(App::make(SuppressionService::class));

        // Add Attribution to Processing
        $service->registerProcessing(App::make(ThirdPartyRecordProcessingService::class));

        return $service;
    }

}