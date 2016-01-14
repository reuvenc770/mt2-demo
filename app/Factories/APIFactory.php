<?php
/**
 * Created by PhpStorm.
 * User: pcunningham
 * Date: 1/14/16
 * Time: 1:25 PM
 */

namespace App\Factories;

use App\Repositories\ReportsRepo;
use App\Services\Interfaces\IReportService;
/**
 * Create different Services for APIS
 * Class APIFactory
 * @package App\Factories
 */
class APIFactory
{
    /**
     * Create an API Service for Reports
     * @param $name
     * @param $accountNumber
     * @return IReportService
     * @throws \Exception
     */
    public function createAPIReportService($name, $accountNumber)
    {
        $reportName = "{$name}Report";
        $reportModelName = "App\\Models\\{$reportName}";
        $reportModel = new $reportModelName();

        $reportServiceName = "App\\Services\\{$reportName}Service";
        if (class_exists($reportServiceName)) {
            return new $reportServiceName(new ReportsRepo($reportModel), $accountNumber);
        } else {
            throw new \Exception("That Report Service does not exist");
        }
    }
}