<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\DataProcessingJob;
use Illuminate\Foundation\Bus\DispatchesJobs;

class PopulateCfsStatsTables extends Command
{
    use DispatchesJobs;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'process:cfsStats {lookback?}';
    protected $name = 'ProcessCfsStats';
    const DEFAULT_LOOKBACK = 5;

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Populate click and open stats for creatives, from lines, and subject lines.';

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
        $lookback = $this->argument('lookback') ?: self::DEFAULT_LOOKBACK;
        $job = new DataProcessingJob($this->name, str_random(16), $lookback);
        $this->dispatch($job);
    }
}
