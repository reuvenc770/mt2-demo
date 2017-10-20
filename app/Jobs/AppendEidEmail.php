<?php

namespace App\Jobs;

use App\Jobs\MonitoredJob;
use App\Services\AppendEidService;
use Storage;

class AppendEidEmail extends MonitoredJob
{

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
    protected $jobName;
    
    const NAME_BASE = "AppendEidEmailJob";

    public function __construct($filePath,$fileName,$feed,$fields,$suppression, $tracking, $threshold)
    {
        $this->filePath = $filePath;
        $this->includeFeed = $feed;
        $this->includeFields = $fields;
        $this->includeSuppression = $suppression;
        $this->fileName = $fileName;

        $jobName = self::NAME_BASE . '-' . $fileName;
        parent::__construct($jobName, $threshold, $tracking);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    protected function handleJob()
    {
        $service = \App::make(\App\Services\AppendEidService::class);
        $ftpPath = "/APPENDEID/{$this->fileName}";
        $csv = $service->createFile($this->filePath, $this->includeFeed, $this->includeFields, $this->includeSuppression);
        Storage::disk('SystemFtp')->put($ftpPath,$csv);
    }
}
