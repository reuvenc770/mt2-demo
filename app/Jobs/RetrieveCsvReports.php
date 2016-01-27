<?php

namespace App\Jobs;

use App\Jobs\Job;
use App\Factories\APIFactory;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Facades\EspAccount;
use App\Models\JobEntry;
use App\Facades\JobTracking;
class RetrieveCsvReports extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;
    CONST JOB_NAME = "RetrieveCsvEspReports";
    /**
     * @var
     */
    protected $apiName;
    /**
     * @var
     */
    protected $accountName;

    protected $filePath;

    protected $tracking;
    protected $maxAttempts;

    /**
     * RetrieveReports constructor.
     * @param $apiName
     * @param $accountName
     * @param $date
     */
    public function __construct($apiName, $accountName, $filePath, $tracking)
    {
        $this->apiName = $apiName;
        $this->accountName = $accountName;
        $this->filePath = $filePath;
        $this->maxAttempts = env('MAX_ATTEMPTS',10);
        $this->tracking = $tracking;

    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        JobTracking::startEspJob(self::JOB_NAME,$this->apiName, $this->accountName, $this->tracking);
        $reportService = APIFactory::createApiReportService($this->apiName,$this->accountName);
        $reportArray = EspAccount::mapCsvToRawStatsArray($this->accountName, $this->filePath);
        $reportService->insertCsvRawStats($reportArray);
        JobTracking::changeJobState(JobEntry::SUCCESS,$this->tracking, $this->attempts());
    }

    public function failed()
    {
        JobTracking::changeJobState(JobEntry::FAILED,$this->tracking, $this->maxAttempts);
    }
}
