<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\AttributionBatchProcessJob;
use Illuminate\Foundation\Bus\DispatchesJobs;

class AttributionBatchProcess extends Command
{
    use DispatchesJobs;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'attribution:processBatch {data} {modelId} {timestamp}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process attribution for the following group of records.';

    private $queue = 'attribution';
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
        $data = $this->argument('data');
        $modelId = $this->argument('modelId');
        $timestamp = $this->argument('timestamp');
        $job = (new AttributionBatchProcessJob($data, $modelId, $timestamp, str_random(16)))->onQueue($this->queue);
        $this->dispatch($job);
    }
}