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

        $reportServiceName = "App\\Services\\{$reportName}Service";
        if (class_exists($reportServiceName)) {
            return new $reportServiceName(new ReportRepo($reportModel), $apiName, $espAccountId);
        } else {
            throw new \Exception("That Report Service does not exist");
        }
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

}