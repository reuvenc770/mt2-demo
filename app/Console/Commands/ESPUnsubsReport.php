<?php

namespace App\Console\Commands;
use App\Jobs\GenerateEspUnsubReport;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Console\Command;
use Carbon\Carbon;
class ESPUnsubsReport extends Command
{
    use DispatchesJobs;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reports:generateEspUnsubReport {--lookback=}';

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
        $lookback = $this->option('lookback') ? $this->option('lookback') : env('LOOKBACK',1);
        $date = Carbon::now()->subDay($lookback)->startOfDay()->toDateString();
        $job = (new GenerateEspUnsubReport($date, str_random(16)));
        $this->dispatch($job);
    }
}
