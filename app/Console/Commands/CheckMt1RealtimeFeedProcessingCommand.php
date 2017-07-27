<?php
/**
 * @author Adam Chin <achin@zetaglobal.com>
 */

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Foundation\Bus\DispatchesJobs;
use App\Jobs\CheckMt1RealtimeFeedProcessingJob;

class CheckMt1RealtimeFeedProcessingCommand extends Command
{
    use DispatchesJobs;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'feedRecords:checkMt1Realtime { --runtime-threshold=30m : Threshold for monitoring. }';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Checks for MT1 realtime processing server for unprocessed files and no realtime data traffic in CMP for the past 2 hours.';

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
        $job = \App::make( CheckMt1RealtimeFeedProcessingJob::class , [
            str_random( 16 ) ,
            $this->option( 'runtime-threshold' )
        ] );
        
        $this->dispatch( $job );
    }
}
