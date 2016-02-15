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
class RetrieveApiReports extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;
    CONST JOB_NAME = "RetrieveApiEspReports";
    protected $apiName;
    protected $espAccountId;
    protected $date;
    protected $maxAttempts;
    protected $tracking;

    public function __construct($apiName, $espAccountId, $date, $tracking)
    {
       $this->apiName = $apiName;
       $this->espAccountId = $espAccountId;
       $this->date = $date;
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
        JobTracking::startEspJob(self::JOB_NAME,$this->apiName, $this->espAccountId, $this->tracking);
        //If it has been retried lets make it wait before it goes back out
        if ($this->attempts() > $this->maxAttempts) {
            $this->release(1);
        }
        $reportService = APIFactory::createAPIReportService($this->apiName,$this->espAccountId);
        $data = $reportService->retrieveApiStats($this->date);
        if($data){
            $reportService->insertApiRawStats($data);
        }
        JobTracking::changeJobState(JobEntry::SUCCESS,$this->tracking, $this->attempts());

    }


    public function failed()
    {
        JobTracking::changeJobState(JobEntry::FAILED,$this->tracking, $this->maxAttempts);
    }
}
