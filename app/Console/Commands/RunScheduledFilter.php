<?php

namespace App\Console\Commands;

use App\Jobs\ScheduledFilterResolver;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Foundation\Bus\DispatchesJobs;

class RunScheduledFilter extends Command
{
    use DispatchesJobs;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'runFilter {filter} {daysback?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run Scheduled Filters {filter} {days lookback} ';

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
        $days = $this->argument('daysback');
        $date = isset($days) ? Carbon::now()->subDays($days)->toDateString() : Carbon::today()->toDateString();
        $job = new ScheduledFilterResolver( $this->argument( 'filter' ), $date , str_random(16));
        $this->dispatch( $job );
    }
}
