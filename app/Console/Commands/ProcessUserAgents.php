<?php

namespace App\Console\Commands;
use Illuminate\Console\Command;
use App\Jobs\ImportContentServerStatsJob;
use Illuminate\Foundation\Bus\DispatchesJobs;

class ProcessUserAgents extends Command
{
    use DispatchesJobs;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'process:useragents';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update the user agent list -> device family mapping';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle() {
        $job = new ProcessUserAgentsJob(str_random(16));
        $this->dispatch($job);
    }
}