<?php

namespace App\Jobs;

use App\Jobs\Job;
use App\Services\SuppressionService;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Facades\JobTracking;
use League\Csv\Writer;
use App\Models\JobEntry;
use File;
use DB;

class SendSuppressionsToMT1 extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;
    protected $tracking;
    protected $date;
    CONST JOB_NAME = "FTPSuppressionsToMT1";
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($date, $tracking)
    {
        $this->date = $date;
        $this->tracking = $tracking;
        JobTracking::startEspJob(self::JOB_NAME,"", "", $this->tracking);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(SuppressionService $service) {
        JobTracking::changeJobState(JobEntry::RUNNING,$this->tracking);
        $filePath = "/MT2/{$this->date}-{$this->tracking}.csv";
        File::put($filePath,null); //create the file
        $query = $service->getAllSuppressionsSinceDate($this->date);

        $query->chunk(10000, function($records) use ($filePath) {
            $writer = Writer::createFromFileObject(new \SplTempFileObject());
            $arrayRecords = $records->toArray();
            $writer->insertAll($arrayRecords);
            File::append($filePath, $writer->__toString());
        });
        
        JobTracking::changeJobState(JobEntry::SUCCESS,$this->tracking);
    }

    public function failed()
    {
        JobTracking::changeJobState(JobEntry::FAILED,$this->tracking);
    }
}
