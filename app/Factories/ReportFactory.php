<?php

namespace App\Factories;
use App\Services\CustomReportService;
use Storage;

/**
 * Create dependencies for ReportServices
 * Class ReportFactory
 * @package App\Factories
 */
class ReportFactory
{
    /**
     * Create an API Service for Reports
     * @param $apiName
     * @param $accountNumber
     * @return mixed
     * @throws \Exception
     */

    public static function createActionsReport($name, $espName, $espAccounts) {

        $service = "App\Services\\" . config("reports.$name.service");
        $repoName = "App\Repositories\\" . config("reports.$name.repo");
        $modelName = "App\Models\\" . config("reports.$name.model");

        try {
            $model = new $modelName();
            $repo = new $repoName($model);
            $destination = Storage::disk(config("reports.$name.destination"));
        }
        catch (\Exception $e) {
            echo "Error instantiating ExportReportService: {$e->getMessage()}";
            throw new \Exception($e->getMessage());
        }

        return new $service($repo, $espName, $espAccounts, $destination);
    }

}