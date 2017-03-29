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
    protected $count;
    CONST JOB_NAME = "FTPSuppressionsToMT1";
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($date, $tracking)
    {
        $this->date = $date;
        $this->count = 0;
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
        $query = $service->getAllSuppressionsSinceDate($this->date);
        $query->chunk(10000, function($records)  {
            $filePath = "/MT2/{$this->date}-{$this->tracking}{$this->count}.csv";
            $writer = Writer::createFromFileObject(new \SplTempFileObject());
            $arrayRecords = $records->toArray();
            $writer->insertAll($arrayRecords);
            File::put($filePath, $writer->__toString());
            $this->count++;
        });
        
        JobTracking::changeJobState(JobEntry::SUCCESS,$this->tracking);
    }

    public function failed()
    {
        JobTracking::changeJobState(JobEntry::FAILED,$this->tracking);
    }
}
