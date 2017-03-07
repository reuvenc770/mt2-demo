<?php

namespace App\Jobs;

use App\DataModels\CacheReportCard;
use App\Jobs\Job;
use Carbon\Carbon;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Mail;
class BuildAndSendReportCard extends Job implements ShouldQueue
{
    CONST DEFAULT_MAIL = "alphateam@zetainteractive.com";
    CONST EMERGENCY_MAIL = "espken@zetaglobal.com";
    use InteractsWithQueue, SerializesModels;
    private $reportCard;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(CacheReportCard $reportCard)
    {
        $this->reportCard = $reportCard;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $mailObject = [
            "date" => Carbon::today()->toDateString(),
            "owner" => $this->reportCard->getOwner(),
            "warning" => false,
            "entries" =>[]
        ];
        $entries = $this->reportCard->getEntries();
        foreach($entries as $entry){
            if(!$mailObject['warning']){
                $mailObject['warning'] = ($entry->getOriginalTotal() - $entry->getFinalTotal()) <= 10;
            }
            $mailObject["entries"][] = [
                "fileName" => $entry->getFileName(),
                "originalTotal" => $entry->getOriginalTotal(),
                "finalTotal" => $entry->getFinalTotal(),
                "globallySuppressed" => $entry->getGloballySuppressed(),
                "listOfferSuppressed" => $entry->getListOfferSuppressed(),
                "offersSuppressedAgainst" => $entry->getOffersSuppressedAgainst(),
            ];
        }

        Mail::send('emails.deploySuppression', $mailObject, function ($message) use ($mailObject) {
            $toEmail = $mailObject['warning'] ? self::EMERGENCY_MAIL : self::DEFAULT_MAIL;
            $message->to($toEmail);
            $message->to("gtdev@zetaglobal.com");
            $subject = $mailObject['warning'] ? "ALERT " : "";
            $subject = "{$subject} Deploy Suppression Report for {$mailObject['owner']}";
            $message->subject($subject);
        });
    }
}
