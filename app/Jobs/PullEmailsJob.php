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
//    private $feedId = array("2803","2833");

    private $feedId;
    private $startdate;
    private $enddate;

//    private $startdate = "2017-10-10 00:00:00" ;
//    private $enddate = "2017-12-15 00:00:00" ;
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
        Log::info('This is some useful d that just ran its course.12344445');
        parent::__construct(self::JOB_NAME,$runtimeThreshold,$tracking);

    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handleJob()
    {
        Log::info('This is some useful information about the command that just ran its course.123');
        $rawService = \App::make(\App\Services\RawFeedEmailService::class);
        $records = $rawService->getPullEmails($this->feedId,$this->startdate, $this->enddate);
	Log::info('This is some useful i');

        //Log::info($records);
        //$repo = \App::make(\App\Repositories\RawFeedEmailRepo::class);
       // $records = $repo->getPullEmails($this->feedId);
       // dd($records);

      	$writer = Writer::createFromFileObject(new \SplTempFileObject());
      	$writer->insertAll($records);
//       	$date = Carbon::today()->toDateString();
	$dt = Carbon::now();
	$datetime = $dt->timestamp;

        //FTP LOCATION TO BE DETERMINED
        Storage::disk("local")->put("FileDownload_{$datetime}.csv", $writer->__toString());
        return count($records);


    }
}

