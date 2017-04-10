<?php
/**
 * Adam Chin <achin@zetaglobal.com>
 */

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Foundation\Bus\DispatchesJobs;

use App\Jobs\SyncModelsWithNewFeedsJob;

class SyncModelsWithNewFeedsCommand extends Command
{
    use DispatchesJobs;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'attribution:syncModelsWithNewFeeds';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        $this->dispatch( new SyncModelsWithNewFeedsJob( str_random( 16 ) ) );
    }
}
