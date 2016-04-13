<?php

namespace App\Console\Commands;
use Illuminate\Console\Command;
use App\Jobs\ImportContentServerStatsJob;
use Illuminate\Foundation\Bus\DispatchesJobs;

class DownloadContentServerStats extends Command
{
    use DispatchesJobs;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'download:mtstats';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create file of MT Content Server stats and ingest';

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
        $job = new ImportContentServerStatsJob(str_random(16));
        $this->dispatch($job);
    }
}
