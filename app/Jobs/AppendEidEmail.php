<?php

namespace App\Jobs;

use App\Jobs\Job;
use App\Services\AppendEidService;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Mail;
use Storage;
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
    private $fileName;
    public function __construct($filePath,$fileName,$feed,$fields,$suppression)
    {
        $this->filePath = $filePath;
        $this->includeFeed = $feed;
        $this->includeFields = $fields;
        $this->includeSuppression = $suppression;
        $this->fileName = $fileName;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(AppendEidService $service)
    {
        $ftpPath = "/APPENDEID/{$this->fileName}";
        $csv = $service->createFile($this->filePath, $this->includeFeed, $this->includeFields, $this->includeSuppression);
        Storage::disk('SystemFtp')->put($ftpPath,$csv);
    }
}
