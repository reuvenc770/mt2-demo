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

        $service = "App\Reports\\" . config("reports.$name.service");
        $repoName = "App\Repositories\\" . config("reports.$name.repo");
        $modelName = "App\Models\\" . config("reports.$name.model");

        try {
            $model = new $modelName();

            if (null !== config("reports.$name.model2")) {
                $model2Name = "App\Models\\" . config("reports.$name.model2");
                $model2 = new $model2Name();
                $repo = new $repoName($model, $model2);
            }
            else {
                $repo = new $repoName($model);
            }

            $destination = Storage::disk(config("reports.$name.destination"));
        }
        catch (\Exception $e) {
            echo "Error instantiating ExportReportService: {$e->getMessage()}";
            throw new \Exception($e->getMessage());
        }
        $s = new $service($repo, $espName, $espAccounts, $destination);

        if (true === config("reports.$name.setRange")) {
            $s->setRange();
        }

        return $s;
    }

}