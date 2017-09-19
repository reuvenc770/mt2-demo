<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Foundation\Bus\DispatchesJobs;
use App\Jobs\OptimizeWorkersJob;

class OptimizeWorkers extends Command
{
    use DispatchesJobs;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'supervisor:optimize {--runtime-threshold=default}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Disable and enable Supervisor workers as necessary.';

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
        $runtimeThreshold = $this->option('runtime-threshold');
        $job = new OptimizeWorkersJob(str_random(16));
        $this->dispatch($job);
    }
}
