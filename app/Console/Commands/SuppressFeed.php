<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Foundation\Bus\DispatchesJobs;
use App\Jobs\FeedSuppressionJob;

class SuppressFeed extends Command
{
    use DispatchesJobs;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'feeds:suppress {feedId}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Initiate feed suppression for a particular feed id.';

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
        $feedId = $this->argument('feedId');
        $job = new FeedSuppressionJob($feedId, str_random(16));
        $this->dispatch($job);
    }
}
