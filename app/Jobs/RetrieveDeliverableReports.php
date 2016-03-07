<?php

namespace App\Jobs;

use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\Models\JobEntry;
use App\Facades\JobTracking;
use App\Factories\APIFactory;

class RetrieveDeliverableReports extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    CONST JOB_NAME = "RetrieveDeliverableReports";

    protected $apiName;
    protected $espAccountId;
    protected $date;
    protected $maxAttempts;
    protected $tracking;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct( $apiName, $espAccountId, $date, $tracking )
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

        if ($this->attempts() > $this->maxAttempts) $this->release(1);

        $reportService = APIFactory::createAPIReportService($this->apiName,$this->espAccountId);

        //Data grab here

        JobTracking::changeJobState(JobEntry::SUCCESS,$this->tracking, $this->attempts());
    }

    public function failed()
    {
        JobTracking::changeJobState(JobEntry::FAILED,$this->tracking, $this->maxAttempts);
    }
}
