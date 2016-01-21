<?php

namespace App\Jobs;

use App\Jobs\Job;
use App\Factories\APIFactory;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Facades\EspAccount;
class RetrieveCsvReports extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    /**
     * @var
     */
    protected $apiName;
    /**
     * @var
     */
    protected $accountName;

    /**
     * RetrieveReports constructor.
     * @param $apiName
     * @param $accountName
     * @param $date
     */
    public function __construct($apiName, $accountName)
    {
        $this->apiName = $apiName;
        $this->accountName = $accountName;

    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $reportService = APIFactory::createApiIeportService($this->apiName,$this->accountName);
        $reportArray = EspAccount::mapCsvToRawStatsArray($this->accountName);
        $reportService->insertCsvRawStats($reportArray);
    }
}
