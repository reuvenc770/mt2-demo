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

    protected $filePath;

    /**
     * RetrieveReports constructor.
     * @param $apiName
     * @param $accountName
     * @param $date
     */
    public function __construct($apiName, $accountName, $filePath)
    {
        $this->apiName = $apiName;
        $this->accountName = $accountName;
        $this->filePath = $filePath;

    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $reportService = APIFactory::createApiReportService($this->apiName,$this->accountName);
        $reportArray = EspAccount::mapCsvToRawStatsArray($this->accountName, $this->filePath);
        $reportService->insertCsvRawStats($reportArray);
    }
}
