<?php

namespace App\Jobs;

use App\Jobs\Job;
use App\Services\DeployService;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Facades\JobTracking;
use App\Models\JobEntry;
use League\Csv\Writer;
use Illuminate\Support\Facades\Storage;
class SendOpsDeploys extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    protected $tracking;
    protected $deploys;
    CONST JOB_NAME = "SendOpsDeploys";

    public function __construct($deploys, $tracking)
    {
        $this->deploys = $deploys;
        $this->tracking = $tracking;
        JobTracking::startEspJob(self::JOB_NAME,"", "", $this->tracking);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(DeployService $service)
    {
        JobTracking::changeJobState(JobEntry::RUNNING,$this->tracking);
        $records = $service->getdeployTextDetailsForDeploys($this->deploys);
        $writer = Writer::createFromFileObject(new \SplTempFileObject());
        $writer->insertOne($service->getHeaderRow());
        $writer->insertAll($records);
        //FTP LOCATION TO BE DETERMINED
        Storage::put("/deploys/{$this->tracking}.csv", $writer->__toString());
        JobTracking::changeJobState(JobEntry::SUCCESS,$this->tracking);
    }

    public function failed()
    {
        JobTracking::changeJobState(JobEntry::FAILED,$this->tracking);
    }

}