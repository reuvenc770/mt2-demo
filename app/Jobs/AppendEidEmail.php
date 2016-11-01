<?php

namespace App\Jobs;

use App\Jobs\Job;
use App\Services\AppendEidService;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Mail;
class AppendEidEmail extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    private $filePath;
    private $includeFeed;
    private $includeFields;
    private $includeSuppression;
    private $email;
    public function __construct($filePath,$email,$feed,$fields,$suppression)
    {
        $this->filePath = $filePath;
        $this->includeFeed = $feed;
        $this->includeFields = $fields;
        $this->includeSuppression = $suppression;
        $this->email = $email;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(AppendEidService $service)
    {
       $csv = $service->createFile($this->filePath, $this->includeFeed, $this->includeFields, $this->includeSuppression);
        Mail::send("emails.append",array(), function ($message) use ($csv) {
            $message->attachData($csv, "results.csv");
            $message->subject("Here are your results for your AppendEID Job");
            $message->priority(1);
            $message->to($this->email);
        });
    }
}
