<?php

namespace App\Jobs;

use App\Jobs\Job;
use App\Services\AppendEidService;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class AppendEidEmail extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    private $filePath;
    private $feed;
    private $fields;
    public function __construct($filePath,$feed,$fields)
    {
        $this->filePath = $filePath;
        $this->feed = $feed;
        $this->fields = $fields;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(AppendEidService $service)
    {
       $test = $service->createFile($this->filePath, $this->feed, $this->fields);
        echo $test;
    }
}
