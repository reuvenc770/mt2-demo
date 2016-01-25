<?php

namespace App\Jobs;

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

    protected $max_attempts;

    protected $job_id;
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
       $this->max_attempts = env('MAX_ATTEMPTS',10);
       $this->job_id = null;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        JobTracking::startEspJob(self::JOB_NAME,$this->apiName, $this->accountName);
        //If it has been retried lets make it wait before it goes back out
        if ($this->attempts() > $this->max_attempts) {
            $this->release(1);
        }
        $reportService = APIFactory::createAPIReportService($this->apiName,$this->accountName);
        $data = $reportService->retrieveReportStats($this->date);
        $reportService->insertRawStats($data);
        JobTracking::finishEspJob(self::JOB_NAME,$this->apiName, $this->accountName, $this->attempts());

    }


    public function failed()
    {
        JobTracking::failEspJob(self::JOB_NAME,$this->apiName, $this->accountName, $this->max_attempts);
    }
}
