<?php
/**
 * Created by PhpStorm.
 * User: pcunningham
 * Date: 1/14/16
 * Time: 1:25 PM
 */

namespace App\Factories;
use App\Models\StandardReport;
use App\Repositories\ReportRepo;
use App\Repositories\TrackingRepo;
use App\Services\API\CakeApi;
use App\Services\API\EspBaseApi;
use App\Repositories\StandardTrackingReportRepo;
use App\Repositories\StandardApiReportRepo;
use App\Services\StandardReportService;
use App\Repositories\EmailRepo;
use App\Repositories\EmailActionsRepo;
use App\Repositories\ActionRepo;

use App\Models\Email;
use App\Models\EmailAction;
use App\Models\ActionType;
use App\Models\EmailDomain;
use App\Models\DomainGroup;
use App\Models\EmailFeedInstance;
use App\Repositories\EmailRecordRepo;
use App\Services\EmailRecordService;

use GuzzleHttp\Client;
use App;

/**
 * Create different Services for APIS
 * Class APIFactory
 * @package App\Factories
 */
class APIFactory
{
    /**
     * Create an API Service for Reports
     * @param $apiName
     * @param $accountNumber
     * @return mixed
     * @throws \Exception
     */
    public static function createApiReportService($apiName, $espAccountId)
    {
        $reportName = "{$apiName}Report";
        $reportModelName = "App\\Models\\{$reportName}";
        $reportModel = new $reportModelName();
        $api = "App\\Services\\API\\{$apiName}Api";

        $emailRecord = App::make(EmailRecordService::class);

        $reportServiceName = "App\\Services\\{$reportName}Service";
        if (class_exists($reportServiceName)) {
            return new $reportServiceName(new ReportRepo($reportModel), new $api($espAccountId) , $emailRecord );
        } else {
            throw new \Exception("That Report Service does not exist");
        }
    }

    public static function createApiSubscriptionService($apiName, $espAccountId){
        if( in_array( $apiName , [
            "BlueHornet" ,
            "EmailDirect" ,
            "Campaigner",
            "Publicators",
            "AWeber",
        ] ) ) {
            $api = "App\\Services\\API\\{$apiName}Api";
            $service = "App\\Services\\{$apiName}SubscriberService";
            return new $service(new $api($espAccountId));
        } else {
            return self::createApiReportService($apiName, $espAccountId);
        }
    }



    public static function createTrackingApiService($source, $startDate, $endDate) 
    {
        $dataName = "{$source}Action";
        $dataModelName = "App\\Models\\{$dataName}";
        $dataModel = new $dataModelName();
        $dataServiceName = "App\\Services\\TrackingDataService";
        $apiName = "App\\Services\\API\\{$source}Api";

        if (class_exists($dataServiceName)) {
            return new $dataServiceName(new TrackingRepo($dataModel), 
                new $apiName($startDate, $endDate), 
                App::make(App\Repositories\EspApiAccountRepo::class));
        } else {
            throw new \Exception("That Tracking Service does not exist");
        }
    }

    public static function createStandardReportService($service) {
        $standardModel = new \App\Models\StandardReport();

        if (is_a($service, 'App\Services\AbstractReportService')) {
            $standardReportRepo = new StandardApiReportRepo($standardModel);
        }
        else {
            $standardReportRepo = new StandardTrackingReportRepo($standardModel);
        }
        
        return new StandardReportService($standardReportRepo);
    }

    public static function createMt1DataImportService($name) {

        switch ($name) {

            case 'DownloadContentServerStats':
                $model = new \App\Models\ContentServerAction();
                $repo = new \App\Repositories\ContentServerActionRepo($model);
                $api = new \App\Services\API\Mt1DbApi();

                return new \App\Services\ImportContentActionsService($api, $repo);

            default:
                break;
        }

    }

    public static function createSimpleStandardReportService() {
        $standardModel = new \App\Models\StandardReport();
        $standardReportRepo = new StandardApiReportRepo($standardModel);
        return new StandardReportService($standardReportRepo);
    }

    public static function createSharedCookieGuzzleClient () {
        return new Client( [ 'cookies' => true ] );
    }
}
