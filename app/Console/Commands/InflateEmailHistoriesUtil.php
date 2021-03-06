<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;
use Illuminate\Foundation\Bus\DispatchesJobs;
use App\Jobs\InflateEmailHistoriesJob;
use App\Console\Traits\PreventOverlapping;

class InflateEmailHistoriesUtil extends Command
{
    use DispatchesJobs, PreventOverlapping;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'utils:inflateEmails';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Inflate email_id_histories into a fully-fledged table for updates';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle() {
        $job = new InflateEmailHistoriesJob(str_random(16));
        $this->dispatch($job);
    }
}