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
    protected $maxAttempts;
    protected $tracking;
    protected $source;

    public function __construct($source, $startDate, $endDate, $tracking) {
       $this->source = $source;
       $this->startDate = $startDate;
       $this->endDate = $endDate;
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
        JobTracking::startTrackingJob(self::JOB_NAME . $this->source,$this->startDate, $this->endDate, $this->tracking);
        if ($this->attempts() > $this->maxAttempts) {
            $this->release(1);
        }

        $dataService= APIFactory::createTrackingApiService($this->source, $this->startDate,$this->endDate);
        $data = $dataService->retrieveTrackingApiStats();

        if($data){
            $dataService->insertApiRawStats($data);
        }
        
        JobTracking::changeJobState(JobEntry::SUCCESS,$this->tracking, $this->attempts());

    }


    public function failed()
    {
        JobTracking::changeJobState(JobEntry::FAILED,$this->tracking, $this->maxAttempts);
    }
}