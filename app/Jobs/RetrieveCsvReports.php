<?php

namespace App\Jobs;

use App\Jobs\Job;
use App\Factories\APIFactory;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Facades\EspApiAccount;
use App\Models\JobEntry;
use App\Facades\JobTracking;
use Storage;
class RetrieveCsvReports extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;
    CONST JOB_NAME = "RetrieveCsvEspReports";
    /**
     * @var
     */
    protected $apiName;
    protected $date;

    protected $filePath;

    protected $tracking;
    protected $maxAttempts;

    /**
     * RetrieveReports constructor.
     * @param $apiName
     * @param $accountName
     * @param $date
     */
    public function __construct($apiName, $filePath, $realDate, $tracking)
    {
        $this->apiName = $apiName;
        $this->filePath = $filePath;
        $this->maxAttempts = env('MAX_ATTEMPTS',10);
        $this->tracking = $tracking;
        $this->date = $realDate;
        JobTracking::startEspJob(self::JOB_NAME,$this->apiName,null, $this->tracking);

    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        JobTracking::changeJobState(JobEntry::RUNNING,$this->tracking);
        $reportService = APIFactory::createApiReportService($this->apiName,0);
        $reportArray = EspApiAccount::mapCsvToRawStatsArray($this->apiName, $this->filePath);
        $reportService->insertCsvRawStats($reportArray, $this->date);
        Storage::delete($this->filePath);
        JobTracking::changeJobState(JobEntry::SUCCESS,$this->tracking);
    }

    public function failed()
    {
        JobTracking::changeJobState(JobEntry::FAILED,$this->tracking);
    }
}
