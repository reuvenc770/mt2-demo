<?php

namespace App\Jobs;

use App\Services\SuppressionService;
use App\Facades\JobTracking;
use League\Csv\Writer;
use App\Models\JobEntry;
use Storage;
use Mail;
use Carbon\Carbon;

class SendSuppressionsToMT1 extends MonitoredJob
{
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
    public function __construct($date, $tracking, $runtimeThreshold)
    {
        $this->date = $date;
        $this->tracking = $tracking;
        parent::__construct(self::JOB_NAME,$runtimeThreshold,$this->tracking);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handleJob() {
        $service = \App::make(\App\Services\SuppressionService::class);

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
                $filePath = "/MT2/{$this->date}-{$this->tracking}{$count}.csv";
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
            $message->to('tech.team.mt2@zetaglobal.com');
            $message->to('espken@zetaglobal.com');
            $message->subject('ESP Suppressions uploaded to MT1 for ' . $yesterday);
        });

        echo "Email sent" . PHP_EOL;

        return $count;
        
    }
}
