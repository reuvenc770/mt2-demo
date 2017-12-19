<?php

namespace App\Jobs;

use App\Services\EmailRecordService;
use Illuminate\Queue\SerializesModels;
use League\Csv\Writer;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Facades\JobTracking;
class PullEmailsJob extends MonitoredJob
{

    /**
     * Create a new job instance.
     *
     * @return void
     */

    private $feedId;
    private $startdate;
    private $enddate;
    CONST JOB_NAME = "EmailPull";
    protected $tracking;
   
    /**
     * PullEMailsJob constructor.
     * @param $feedId
     * @param $jobName
     * @param $tracking
     */
    public function __construct($feedId, $startdate, $enddate, $tracking,$runtimeThreshold)
    {
        $this->tracking = $tracking;
        $this->feedId = $feedId;
        $this->startdate = $startdate;
        $this->enddate = $enddate;
        parent::__construct(self::JOB_NAME,$runtimeThreshold,$tracking);

    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handleJob()
    {
        $rawService = \App::make(\App\Services\RawFeedEmailService::class);
        $records = $rawService->getPullEmails($this->feedId,$this->startdate, $this->enddate);
        $writer = Writer::createFromFileObject(new \SplTempFileObject());
        $writer->insertAll($records);
        $dt = Carbon::now();
        $datetime = $dt->timestamp;
        Storage::disk("local")->put("FileDownload_{$datetime}.csv", $writer->__toString());
        return count($records);
    }
}

