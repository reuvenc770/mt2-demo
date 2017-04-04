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
    protected $signature = 'reports:downloadTrackingData {trackingSource} {lookBack?} {processType?} {--s|startDate=none} {--e|endDate=none}';

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
        $trackingSource = $this->argument('trackingSource');
        $lookBack = $this->argument('lookBack') ?: config('jobs.defaultLookback');

        if ( $this->argument( 'processType' ) == 'record' ) {
            $processType = RetrieveTrackingDataJob::PROCESS_TYPE_RECORD;

            $start = Carbon::today()->subDays($lookBack)->toDateString();
            $end = Carbon::today()->toDateString();

            if ( $this->option( 'startDate' ) != 'none' && $this->option( 'endDate' ) != 'none' ) {
                $start = $this->option( 'startDate' );
                $end = $this->option( 'endDate' );
            }
        }
        else {
            $processType = RetrieveTrackingDataJob::PROCESS_TYPE_ACTION;
            $start = Carbon::now()->subHours($lookBack)->toDateTimeString();
            $end = Carbon::now()->toDateTimeString();
        }

        $cakeLog = "Running {$trackingSource} from {$start} to {$end}";
        $this->info($cakeLog);
        $this->dispatch(
            new RetrieveTrackingDataJob(
                $trackingSource,
                $start,
                $end,
                str_random( 16 ) ,
                $processType
            )
        );

    }
}
