<?php

namespace App\Jobs;

use App\Services\DomainService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Foundation\Bus\DispatchesJobs;
use App\Models\JobEntry;
use App\Facades\JobTracking;

class domainExpirationNotifications extends MonitoredJob
{
    use DispatchesJobs;
    private $days = [30,21,14,7,6,5,4,3,2,1];
    CONST JOB_NAME = 'ExpiredDomains';
    protected $tracking;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($tracking,$runtimeThreshold)
    {
        $this->tracking = $tracking;
        parent::__construct(self::JOB_NAME,$runtimeThreshold,$tracking);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handleJob()
    {
        $domainService = \App::make(\App\Services\DomainService::class);

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
                    $message->to(config( 'mail.defaultMail' ));
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
