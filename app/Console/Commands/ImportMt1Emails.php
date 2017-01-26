<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Foundation\Bus\DispatchesJobs;

use App\Jobs\ImportMt1EmailsJob;
use App\Console\Traits\PreventOverlapping;

class ImportMt1Emails extends Command
{
    use DispatchesJobs, PreventOverlapping;
    const JOB_NAME = "ImportMt1Emails";
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'emails:download {mod}';

    protected $description = 'Pull all emails from the MT1 temp table';


    public function __construct() {
        parent::__construct();
    }

    public function handle() {
        // set up new job, only if another is not running
        $mod = $this->argument('mod');

        if (!$this->isRunning(self::JOB_NAME . '-' . $mod)) {
            $job = new ImportMt1EmailsJob($mod, str_random(16));
            $this->dispatch($job);
        }
        else {
            echo "job not running" . PHP_EOL;
        }

    }
}