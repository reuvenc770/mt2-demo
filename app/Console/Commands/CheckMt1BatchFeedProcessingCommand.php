<?php
/**
 * @author Adam Chin <achin@zetaglobal.com>
 */

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Foundation\Bus\DispatchesJobs;
use App\Jobs\CheckMt1BatchFeedProcessingJob;

class CheckMt1BatchFeedProcessingCommand extends Command
{
    use DispatchesJobs;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'feedRecords:checkMt1Batch { --runtime-threshold=15m : Threshold for monitoring. }';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Checks if there are any old files that were not moved for MT1 and creates missing MT1 directories.';

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
        $job = \App::make( CheckMt1BatchFeedProcessingJob::class , [
            str_random( 16 ) ,
            $this->option( 'runtime-threshold' )
        ] );
        
        $this->dispatch( $job );
    }
}
