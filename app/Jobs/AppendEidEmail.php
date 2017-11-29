<?php

namespace App\Jobs;

use App\Jobs\MonitoredJob;
use App\Services\AppendEidService;
use Storage;
use File;
use Mail;

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
    private $email;
    
    const NAME_BASE = "AppendEidEmailJob";

    public function __construct($inputPath, $outputPath, $fileName, $options, $email, $tracking, $threshold)
    {
        $this->inputPath = $inputPath;
        $this->outputPath = $outputPath;
        $this->fileName = $fileName;
        $this->email = $email;
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
        $service->createFile($this->inputPath, $this->outputPath, $this->options);
        $stream = File::get($this->outputPath);
        Storage::disk('SystemFtp')->put($ftpPath, $stream);
        
        Mail::raw("Append EID completed for {$this->fileName}. File is located at {$ftpPath}", function ($message) {
            $message->subject('Append EID completed');
            $message->to($this->email);
        });
    }
}
