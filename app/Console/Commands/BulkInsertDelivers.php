<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Foundation\Bus\DispatchesJobs;

class BulkInsertDelivers extends Command
{
    use DispatchesJobs;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'insert:delivers {lookback?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "insert X days worth of delivers into email_actions";

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
    public function handle()
    {
        $lookBack = $this->argument('lookback') ? $this->argument('lookback') : 2;
        $job = new \App\Jobs\BulkInsertDelivers($lookBack, str_random(16));
        $this->dispatch($job);
    }
}
