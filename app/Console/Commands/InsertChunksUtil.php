<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;
use Illuminate\Foundation\Bus\DispatchesJobs;
use App\Jobs\InsertChunksJob;
use App\Console\Traits\PreventOverlapping;

class InsertChunksUtil extends Command
{
    use DispatchesJobs, PreventOverlapping;
    const JOB_NAME = 'InsertInChunks';

    private $defaultSize = 50000;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'utils:chunkInsert {from} {to} {size?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Allows us to INSERT INTO ... SELECT from one table to another with similar schemas';

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
        if (!$this->isRunning(self::JOB_NAME)) {
            $size = $this->argument('size') ? $this->argument('size') : $this->defaultSize;
            $from = $this->argument('from');
            $to = $this->argument('to');
            $job = new InsertChunksJob($from, $to, $size, str_random(16));
            $this->dispatch($job);
        }
        else {
            echo "InsertChunks job for $from to $to already running" . PHP_EOL;
        }

    }
}