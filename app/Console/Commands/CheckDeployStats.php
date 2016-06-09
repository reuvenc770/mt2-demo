<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\CheckDeployStatsJob;
use Illuminate\Foundation\Bus\DispatchesJobs;

class CheckDeployStats extends Command
{
    use DispatchesJobs;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reports:findIncompleteDeploys {lookback?}';
    const DEFAULT_LOOKBACK = 7;

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Find all deploys whose record-level stats 
    do not match up with their aggregated stats. Using a non-default lookback 
    value can make this job take an inordinate amount of time.';

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
        $lookback = $this->argument('lookback') ? $this->argument('lookback') : self::DEFAULT_LOOKBACK;
        $job = new CheckDeployStatsJob($lookback, str_random(16));
        $this->dispatch($job);
    }
}
