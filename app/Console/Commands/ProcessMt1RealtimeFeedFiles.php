<?php
/**
 * @author Adam Chin <achin@zetaglobal.com>
 */

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Foundation\Bus\DispatchesJobs;
use App\Jobs\ProcessMt1RealtimeFeedFilesJob;

class ProcessMt1RealtimeFeedFiles extends Command
{
    use DispatchesJobs;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'feedRecords:processMt1RealtimeFiles { --runtime-threshold=15m : Threshold for monitoring. }';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Processes new realtime feed files from MT1 posts server.';

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
        $job = \App::make( ProcessMt1RealtimeFeedFilesJob::class , [
            str_random( 16 ) ,
            $this->option( 'runtime-threshold' )
        ] );

        $this->dispatch($job->onQueue( 'rawFeedProcessing' ));
    }
}
