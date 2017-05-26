<?php
/**
 * @author Adam Chin <achin@zetaglobal.com>
 */

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Foundation\Bus\DispatchesJobs;
use App\Jobs\ProcessFeedRawFilesJob;
use App\Jobs\CMPTE\BatchProcessingJob;
use App\Jobs\CMPTE\RealtimeProcessingJob;

class ProcessFeedRawFiles extends Command
{
    use DispatchesJobs;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'feedRecords:processRawFiles {--M|mode=0}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Processes new feed files for the day.';

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
        if ( $this->option( 'mode' ) == 1 ) {
            $job = new BatchProcessingJob(str_random(16));
        } else if ( $this->option( 'mode' ) == 2 ) {
            $job = new RealtimeProcessingJob(str_random(16));
        } else {
            $job = new ProcessFeedRawFilesJob(str_random(16));
        }

        $this->dispatch($job);
    }
}
