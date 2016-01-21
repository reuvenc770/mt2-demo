<?php

namespace App\Jobs;

use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Factories\APIFactory;
use Illuminate\Support\Facades\Log;

/**
 * Class RetrieveReports
 * @package App\Jobs
 */
class RetrieveApiReports extends Job implements ShouldQueue
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
     * @var
     */
    protected $date;

    /**
     * RetrieveReports constructor.
     * @param $apiName
     * @param $accountName
     * @param $date
     */
    public function __construct($apiName, $accountName, $date)
    {
       $this->apiName = $apiName;
       $this->accountName = $accountName;
       $this->date = $date;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $reportService = APIFactory::createApiReportService($this->apiName,$this->accountName);
        $xmlBody = $reportService->retrieveApiReportStats($this->date);
        $reportService->insertApiRawStats($xmlBody);
    }
}
