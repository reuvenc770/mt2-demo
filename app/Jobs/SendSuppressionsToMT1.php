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
use Storage;
use DB;
use Mail;
use Carbon\Carbon;

class SendSuppressionsToMT1 extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;
    protected $tracking;
    protected $date;
    CONST JOB_NAME = "FTPSuppressionsToMT1";
    private $target = 'gtddev@zetaglobal.com';
    const ROW_COUNT_LIMIT = 10000;

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
        JobTracking::changeJobState(JobEntry::RUNNING, $this->tracking);

        $yesterday = Carbon::yesterday()->toDateString();
        $mailAssoc = $service->createDailyMailAssoc($yesterday);

        $greatTotal = $service->getTotalSinceDate($this->date);
        $segmentedTotal = 0;
        $count = 0;
        $startPoint = $service->getMinIdForDate($this->date);
        $endPoint = $service->getMaxId();

        while ($startPoint < $endPoint) {
            $segmentEnd = $service->nextNRows($startPoint, self::ROW_COUNT_LIMIT);
            $segmentEnd = $segmentEnd ?: $endPoint;

            $data = $service->pullSuppressionsBetweenIds($startPoint, $segmentEnd);

            if ($data) {
                $filePath = "/MT2/{$this->date}-{$this->tracking}{$this->count}.csv";
                $writer = Writer::createFromFileObject(new \SplTempFileObject());

                $writer->insertAll($data->toArray());
                Storage::disk('MT1SuppressionDropOff')->put($filePath, $writer->__toString());
                $count++;
                $startPoint = $segmentEnd;
            }
            else {
                // No data received
                $startPoint = $segmentEnd;
            }

        }

        $uploadMiscount = $greatTotal > $segmentedTotal;

        Mail::send('emails.SuppressionReport', $mailAssoc, function ($message) use ($mailAssoc, $yesterday) {
            $message->to('rbertorelli@zetaglobal.com');
            $message->subject('ESP Suppressions uploaded to MT1 for ' . $yesterday);
        });

        echo "Email sent" . PHP_EOL;
        
        JobTracking::changeJobState(JobEntry::SUCCESS, $this->tracking);
    }

    public function failed()
    {
        JobTracking::changeJobState(JobEntry::FAILED, $this->tracking);
    }
}
