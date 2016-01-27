<?php

namespace App\Jobs;

use App\Models\JobEntry;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Facades\JobTracking;
use App\Factories\APIFactory;

/**
 * Class RetrieveReports
 * @package App\Jobs
 */
class RetrieveReports extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;
    CONST JOB_NAME = "RetrieveApiEspReports";
    protected $apiName;
    protected $accountName;
    protected $date;
    protected $max_attempts;
    protected $tracking;

    public function __construct($apiName, $accountName, $date, $tracking)
    {
       $this->apiName = $apiName;
       $this->accountName = $accountName;
       $this->date = $date;
       $this->max_attempts = env('MAX_ATTEMPTS',10);
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
        //If it has been retried lets make it wait before it goes back out
        if ($this->attempts() > $this->max_attempts) {
            $this->release(1);
        }
        $reportService = APIFactory::createAPIReportService($this->apiName,$this->accountName);
        $data = $reportService->retrieveReportStats($this->date);
        $reportService->insertRawStats($data);
        JobTracking::changeJobState(JobEntry::SUCCESS,$this->tracking, $this->attempts());

    }


    public function failed()
    {
        JobTracking::changeJobState(JobEntry::FAILED,$this->tracking, $this->max_attempts);
    }
}
