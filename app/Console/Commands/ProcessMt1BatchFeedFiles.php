<?php
/**
 * @author Adam Chin <achin@zetaglobal.com>
 */

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Foundation\Bus\DispatchesJobs;
use App\Jobs\ProcessMt1BatchFeedFilesJob;

class ProcessMt1BatchFeedFiles extends Command
{
    use DispatchesJobs;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'feedRecords:processMt1BatchFiles { --runtime-threshold=15m : Threshold for monitoring. }';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Processes new batch feed files from MT1 posts server.';

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
        $job = \App::make( ProcessMt1BatchFeedFilesJob::class , [
            str_random( 16 ) ,
            $this->option( 'runtime-threshold' )
        ] );

        $this->dispatch($job->onQueue( 'rawFeedProcessing' ));
    }
}
