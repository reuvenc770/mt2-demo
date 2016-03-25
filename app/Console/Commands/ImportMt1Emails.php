<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Foundation\Bus\DispatchesJobs;

use App\Jobs\ImportMt1EmailsJob;

class ImportMt1Emails extends Command
{
    use DispatchesJobs;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'emails:download';

    protected $description = 'Pull all emails from the MT1 temp table';


    public function __construct() {
        parent::__construct();
    }

    public function handle() {
        // set up new job

        $job = new ImportMt1EmailsJob(str_random(16));
        $this->dispatch($job);
    }
}