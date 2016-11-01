<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use Illuminate\Foundation\Bus\DispatchesJobs;

use App\Jobs\SyncMT1FeedLevels as SyncJob;

class SyncMT1FeedLevels extends Command
{
    use DispatchesJobs;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'attribution:mt1LevelSync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command fires a job to update Feed levels with MT1.';

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
        $this->dispatch( new SyncJob( str_random( 16 ) ) );
    }
}
