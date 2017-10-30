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
    private $inputPath;
    private $outputPath;
    private $options;
    private $fileName;
    protected $jobName;
    
    const NAME_BASE = "AppendEidEmailJob";

    public function __construct($inputPath, $outputPath, $fileName, $options, $tracking, $threshold)
    {
        $this->inputPath = $inputPath;
        $this->outputPath = $outputPath;
        $this->fileName = $fileName;

        $this->options = $options;

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
        $csv = $service->createFile($this->inputPath, $this->outputPath, $this->options);
        Storage::disk('SystemFtp')->put($ftpPath,$csv);
    }
}
