<?php
/**
 * Created by PhpStorm.
 * User: pcunningham
 * Date: 1/14/16
 * Time: 1:25 PM
 */

namespace App\Factories;
use App\Repositories\ReportRepo;

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
    public static function createAPIReportService($apiName, $accountNumber)
    {
        $reportName = "{$apiName}Report";
        $reportModelName = "App\\Models\\{$reportName}";
        $reportModel = new $reportModelName();

        $reportServiceName = "App\\Services\\{$reportName}Service";
        if (class_exists($reportServiceName)) {
            return new $reportServiceName(new ReportRepo($reportModel), $apiName, $accountNumber);
        } else {
            throw new \Exception("That Report Service does not exist");
        }
    }

}