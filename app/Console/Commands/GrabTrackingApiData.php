<?php

namespace App\Console\Commands;


use App\Repositories\TrackingRepo;
use Carbon\Carbon;
use App\Jobs\RetrieveTrackingDataJob; 
use Illuminate\Console\Command;
use Illuminate\Foundation\Bus\DispatchesJobs;

class GrabTrackingApiData extends Command
{
    use DispatchesJobs;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reports:downloadTrackingData {trackingSource} {lookBack?}';
    protected $trackingRepo;
    protected $lookBack;
    protected $trackingSource;

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';


    public function __construct() {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->trackingSource = $this->argument('trackingSource');
        $lookbackValue = $this->argument('lookBack');
        $this->lookBack = (null !== $lookbackValue) ? $this->argument('lookBack') : env('LOOKBACK',5);

        $startDate = Carbon::now()->subDay($this->lookBack)->toDateString();
        $endDate = Carbon::now()->toDateString();

        $cakeLog = "Running Cake from {$startDate} to {$endDate}";
        $this->info($cakeLog);
        $this->dispatch(new RetrieveTrackingDataJob($this->trackingSource, $startDate, $endDate, str_random(16)));

    }
}