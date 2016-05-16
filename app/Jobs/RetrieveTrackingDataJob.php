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
class RetrieveTrackingDataJob extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;
    CONST JOB_NAME = "RetrieveTrackingApiData-";
    protected $startDate;
    protected $endDate;

    protected $tracking;
    protected $source;

    public function __construct($source, $startDate, $endDate, $tracking) {
       $this->source = $source;
       $this->startDate = $startDate;
       $this->endDate = $endDate;
       $this->tracking = $tracking;
       JobTracking::startTrackingJob(self::JOB_NAME . $this->source,$this->startDate, $this->endDate, $this->tracking);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        JobTracking::changeJobState(JobEntry::RUNNING,$this->tracking);
        $dataService= APIFactory::createTrackingApiService($this->source, $this->startDate,$this->endDate);
        $data = $dataService->retrieveApiStats(null);
        $dataLength = sizeof($data);

        if($data && $dataLength < 10000) {
            $dataService->insertApiRawStats($data);
        }
        elseif ($data && $dataLength > 10000) {
            $dataService->insertSegmentedApiRawStats($data, $dataLength);
        }
        
        JobTracking::changeJobState(JobEntry::SUCCESS,$this->tracking);

    }


    public function failed()
    {
        JobTracking::changeJobState(JobEntry::FAILED,$this->tracking);
    }
}