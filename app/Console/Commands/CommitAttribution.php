<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\CommitAttributionJob;
use Illuminate\Foundation\Bus\DispatchesJobs;

class CommitAttribution extends Command
{
    use DispatchesJobs;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'attribution:commit {modelId?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run attribution based on the current model.';

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
        $model = $this->argument('modelId') ?: 'none';
        $job = new CommitAttributionJob($model, str_random(16));
        $this->dispatch($job);
    }
}