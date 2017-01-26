<?php

namespace App\Factories;
use App\Services\CustomReportService;
use Storage;
use App\Models\EspAccount;
use App\Repositories\EspApiAccountRepo;
use App;

/**
 * Create dependencies for ReportServices
 * Class ReportFactory
 * @package App\Factories
 */
class ReportFactory
{
    /**
     * Create an API Service for Reports
     * @param $name - report name
     * @return mixed
     * @throws \Exception
     */

    public static function createReport($name) {
        $reportType = config("reports.$name.type");

        if ('esp' === $reportType) {
            return self::createEspActionReport($name);
        }
        else {
            return self::createOfferActionReport($name);
        }
    }

    protected static function createEspActionReport($name) {

        $espAccountRepo = new EspApiAccountRepo(new EspAccount());

        $espName = config("reports.$name.data.esp");

        $espAccountConfig = config("reports.$name.data.accounts") ?: 'all';
        $espAccounts = $espAccountConfig === 'all' ? 
            $espAccountRepo->getAccountsByESPName($espName) : 
            self::getEspInfoForAccounts($espAccountRepo, $espAccountConfig);

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
        return new $service($repo, $espName, $espAccounts, $destination);
    }


    protected static function createOfferActionReport($name) {
        $service = "App\Reports\\" . config("reports.$name.service");
        $repoName = "App\Repositories\\" . config("reports.$name.repo");
        $modelName = "App\Models\\" . config("reports.$name.model");

        $advertisers = config("reports.$name.data.advertisers");

        $formatStrategy = "App\\Reports\\Strategies\\" . config("reports.$name.data.formatStrategy");
        $formatStrategy = new $formatStrategy();
        
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

            $sourceService = App::make('App\Services\MT1Services\CompanyService');

            $destination = Storage::disk(config("reports.$name.destination"));
        }
        catch (\Exception $e) {
            throw new \Exception("Error instantiating ReportService for $name: {$e->getMessage()}");
        }
        return new $service($sourceService, $repo, $formatStrategy, $advertisers, $destination);
    }

    protected static function getEspInfoForAccounts($espAccountRepo, $accounts) {
        $output = [];

        foreach ($accounts as $accountName) {
            $output[] = $espAccountRepo->getEspInfoByAccountName($accounts);
        }
        
        return $output;
    }

}