<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Foundation\Bus\DispatchesJobs;
use App\Console\Traits\PreventOverlapping;
use App\Jobs\ProcessFeedRecordsJob;

class ProcessFeedRecords extends Command
{

    use DispatchesJobs, PreventOverlapping;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'feedRecords:process {records}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process provided feed records.';

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
        $records = $this->argument('records');

        // will this actually be coming in with party information?
        // We could run 1st party pullers more frequently ...
        // Maybe this should pick up the records themselves

        $job = new ProcessFeedRecordsJob();
        $this->dispatch($job);
    }
}
