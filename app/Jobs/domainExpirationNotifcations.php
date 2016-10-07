<?php

namespace App\Jobs;

use App\Jobs\Job;
use App\Services\DomainService;
use Carbon\Carbon;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

class domainExpirationNotifcations extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;
    private $days = [30,21,14,7,6,5,4,3,2,1];
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {

    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(DomainService $domainService, Mail $mailer)
    {
        $domains = array();
        foreach($this->days as $day){
            $domains = $domainService->getExpiringDomainsByDate($this->daysOut($day));
            $numberOfDomains = count($domains);
            if($numberOfDomains > 0){
                if($day < 4){
                    $subject = "URGENT:{$numberOfDomains} domains are expiring in {$day} day(s)";
                }
                else if($day <= 7){
                    $subject = "Urgent: {$numberOfDomains} domains are expiring in one week!";
                } else{
                     $subject = "Notice: {$numberOfDomains} domains are expiring soon!";
                }

                Mail::send('emails.expiration', ['domains' => $domains, 'day' => $day], function ($message) use ($domains, $subject) {
                    $message->to(env("EXPIRE_EMAIL","espken@zetainteractive.com"));
                    $message->subject($subject);
                    $message->priority(1);
                });
            }
        }
    }




    private function daysOut($days){
        $today = Carbon::today();
        return $today->addDays($days)->toDateString();
    }
}
