<?php
/**
 * Created by PhpStorm.
 * User: pcunningham
 * Date: 1/14/16
 * Time: 1:25 PM
 */

namespace App\Factories;
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
use App\Models\EmailClientInstance;
use App\Repositories\EmailRecordRepo;
use App\Services\EmailRecordService;

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

        $emailRecord = new EmailRecordService(
            new EmailRecordRepo(
                new Email() ,
                new EmailAction() ,
                new ActionType() ,
                new EmailDomain() ,
                new DomainGroup() ,
                new EmailClientInstance()
            )
        );

        $reportServiceName = "App\\Services\\{$reportName}Service";
        if (class_exists($reportServiceName)) {
            return new $reportServiceName(new ReportRepo($reportModel), new $api($espAccountId) , $emailRecord );
        } else {
            throw new \Exception("That Report Service does not exist");
        }
    }

    public static function createApiSubscriptionService($apiName, $espAccountId){
        $api = "App\\Services\\API\\{$apiName}Api";
        $service = "App\\Services\\{$apiName}SubscriberService";
        return new $service(new $api($espAccountId));
    }

    public static function createCsvDeliverableService($espId, $espName) {

        $emailModel = new \App\Models\Email();
        $actionsModel = new \App\Models\EmailAction();

        $actionTableRepo = new ActionRepo(new ActionType());
        $emailActionRepo = new EmailActionsRepo($actionsModel);
        $emailRepo = new EmailRepo($emailModel);

        $map = new \App\Models\DeliverableCsvMapping();
        $mappingRepo = new \App\Repositories\DeliverableMappingRepo($map);
        $mapping = $mappingRepo->getMapping($espId);

        return new \App\Services\CsvDeliverableService($emailActionRepo, $emailRepo, $actionTableRepo, $mapping);
    }

    public static function createTrackingApiService($source, $startDate, $endDate) 
    {
        $dataName = "{$source}Data";
        $dataModelName = "App\\Models\\{$dataName}";
        $dataModel = new $dataModelName();
        $dataServiceName = "App\\Services\\TrackingDataService";
        $apiName = "App\\Services\\API\\{$source}Api";

        if (class_exists($dataServiceName)) {
            return new $dataServiceName($source, new TrackingRepo($dataModel), new $apiName($startDate, $endDate));
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

            case 'ImportMt1Emails':
                $model = new \App\Models\TempStoredEmail();
                $repo = new \App\Repositories\TempStoredEmailRepo($model);
                $api = new \App\Services\API\Mt1DbApi();

                // need emails, email_client_instances

                $emailModel = new \App\Models\Email();
                $emailRepo = new \App\Repositories\EmailRepo($emailModel);
                $emailClientModel = new \App\Models\EmailClientInstance();
                $emailClientRepo = new \App\Repositories\EmailClientInstanceRepo($emailClientModel);

                $clientModel = new \App\Models\Client();
                $clientRepo = new \App\Repositories\ClientRepo($clientModel);

                $domainModel = new \App\Models\EmailDomain();
                $domainRepo = new \App\Repositories\EmailDomainRepo($domainModel);

                return new \App\Services\ImportMt1EmailsService(
                    $api, 
                    $repo, 
                    $emailRepo, 
                    $emailClientRepo,
                    $clientRepo,
                    $domainRepo);

            case 'DownloadContentServerStats':
                $model = new \App\Models\ContentServerAction();
                $repo = new \App\Repositories\ContentServerActionRepo($model);
                $api = new \App\Services\API\Mt1DbApi();

                return new \App\Services\ImportContentActionsService($api, $repo);

            default:
                break;
        }

    }

}
