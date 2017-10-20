<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Foundation\Bus\DispatchesJobs;

class WarnOldIPv6 extends Command
{
    use DispatchesJobs;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ipv6:warn {--runtime-threshold=default}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Warn tech of expired ipv6 file.";

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
    public function handle()
    {
        $threshold = $this->option('runtime-threshold');
        $job = new \App\Jobs\WarnIPv6Update(str_random(16), $threshold);
        $this->dispatch($job);
    }
}
