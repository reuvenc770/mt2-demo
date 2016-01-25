<?php

namespace App\Jobs;

use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Facades\JobTracking;
use App\Factories\APIFactory;
use Maknz\Slack\Facades\Slack;

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
        JobTracking::startEspJob(self::JOB_NAME,$this->apiName, $this->accountName);
        //If it has been retried lets make it wait before it goes back out
        if ($this->attempts() > 3) {
            $this->release(10);
        }
        $reportService = APIFactory::createAPIReportService($this->apiName,$this->accountName);
        $data = $reportService->retrieveReportStats($this->date);
        $reportService->insertRawStats($data);

    }


    public function failed()
    {
        $name = self::JOB_NAME;
        $string = "{$name} - {$this->apiName} Failed for account {$this->accountName}";
        Slack::to('#mt2-dev-failed-jobs')->send($string);
    }
}
