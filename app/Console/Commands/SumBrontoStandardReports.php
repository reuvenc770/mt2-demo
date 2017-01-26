<?php

namespace App\Console\Commands;

use App\Jobs\SumBrontoRawStats;
use Illuminate\Console\Command;
use Illuminate\Foundation\Bus\DispatchesJobs;

class SumBrontoStandardReports extends Command
{
    use DispatchesJobs;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reports:sumBronto';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Updates Standard reports with the counts from all the raw reports.  This job is dumb and kinda a stop-gap till 1st party data becomes something else';

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
        $job = new SumBrontoRawStats(str_random(16));
        $this->dispatch($job);
    }
}
