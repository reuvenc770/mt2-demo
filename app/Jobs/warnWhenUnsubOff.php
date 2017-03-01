<?php

namespace App\Jobs;

use App\Jobs\Job;
use App\Models\EspAccount;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\JobEntry;
use App\Facades\JobTracking;
use App\Exceptions\JobException;
use Mail;
class warnWhenUnsubOff extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;
    protected $tracking;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($tracking)
    {
        $this->tracking = $tracking;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        JobTracking::changeJobState(JobEntry::RUNNING,$this->tracking);
        try{
            $accounts = EspAccount::where("enable_suppression",false)->get();
            Mail::send('emails.expiration', ['espAccounts' => $accounts], function ($message) use ($accounts) {
                $message->to(config( 'mail.defaultMail' ));
                $message->subject("There are ESP Accounts that are not processing unsubs.");
                $message->priority(1);
            });

        } catch(\Exception $e){
            throw new JobException("Could warn about unsubs not being off{$e->getMessage()}");
        }
        JobTracking::changeJobState(JobEntry::SUCCESS,$this->tracking);
    }

    public function failed() {
        JobTracking::changeJobState(JobEntry::FAILED,$this->tracking);
    }
}
