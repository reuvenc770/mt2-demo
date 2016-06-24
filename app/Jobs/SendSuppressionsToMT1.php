<?php

namespace App\Jobs;

use App\Jobs\Job;
use App\Services\SuppressionService;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Facades\JobTracking;
use App\Models\JobEntry;
use League\Csv\Writer;
use Illuminate\Support\Facades\Storage;
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
    public function handle(SuppressionService $service){
        JobTracking::changeJobState(JobEntry::RUNNING,$this->tracking);
        $records = $service->getAllSuppressionsSinceDate($this->date);
        $writer = Writer::createFromFileObject(new \SplTempFileObject());
        $records = array_flatten($records->toArray());
        $trimmedRecords = array_unique($records);
        $writer->insertAll($trimmedRecords);
        Storage::disk("MT1SuppressionDropOff")->put("/MT2/{$this->date}-{$this->tracking}.csv", $writer->__toString());
        JobTracking::changeJobState(JobEntry::SUCCESS,$this->tracking);
    }

    public function failed()
    {
        JobTracking::changeJobState(JobEntry::FAILED,$this->tracking);
    }
}
