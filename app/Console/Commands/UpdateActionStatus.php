<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Foundation\Bus\DispatchesJobs;
use App\Jobs\DataProcessingJob;

class UpdateActionStatus extends Command
{

    use DispatchesJobs;
    const DEFAULT_LOOKBACK = 1;
    const JOB_NAME = 'UpdateUserActions';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'updateUserActions {lookback?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Updates status information for users based off of recent actions';

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
        // Default of yesterday->today (this will catch reruns)
        $lookback = $this->argument('lookback') ?: self::DEFAULT_LOOKBACK;

        $job = new DataProcessingJob(self::JOB_NAME, str_random(16), $lookback);
        $this->dispatch($job);
    }
}
