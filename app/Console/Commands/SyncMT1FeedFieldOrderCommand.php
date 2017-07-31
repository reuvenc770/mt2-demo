<?php
/**
 * @author Adam Chin <achin@zetaglobal.com>
 */

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Foundation\Bus\DispatchesJobs;
use App\Jobs\SyncMT1FeedFieldOrder;

class SyncMT1FeedFieldOrderCommand extends Command
{
    use DispatchesJobs;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'feedRecords:syncFeedFileColumnOrder { --runtime-threshold=15m : Threshold for monitoring. }';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Syncs the feed file column order from MT1 for parsing feed files from clients.';

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
        $job = \App::make( SyncMT1FeedFieldOrder::class , [
            str_random( 16 ) ,
            $this->option( 'runtime-threshold' )
        ] );
        
        $this->dispatch( $job );
    }
}
