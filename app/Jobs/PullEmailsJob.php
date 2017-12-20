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
    CONST MIN_VALUE = 0;
    CONST READ_THRESHOLD = 10;
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
        $records = $rawService->getPullEmails($this->feedId,$this->startdate, $this->enddate,self::MIN_VALUE,self::READ_THRESHOLD);
        $count = $records->count();
        $maxId = null;
        $output = [];

        while ($count  > 0) {
            $resource = $records->cursor();
            foreach ($resource as $row) {
                $maxId =  $row->id;
                $output[] = $row->email_address.','.$row->source_url.','.$row->ip.','.$row->capture_date;
            }
           
            $writer = Writer::createFromFileObject(new \SplTempFileObject());
            $writer->insertAll($output);
            Storage::disk("local")->put("FileDownload_sample.csv", $writer->__toString());
            $records = $rawService->getPullEmails($this->feedId,$this->startdate, $this->enddate,$maxId,self::READ_THRESHOLD);
            $count = $records->count();
        }
    }
}

