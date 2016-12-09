<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Foundation\Bus\DispatchesJobs;
use App\Jobs\UpdateFeedCountJob;
use Carbon\Carbon;

class UpdateFeedCounts extends Command
{
    use DispatchesJobs;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'feedRecords:updateCounts {--L|lookback=1} {--S|startDate=0} {--E|endDate=0}';

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
        $lookback = $this->option( 'lookback' );
        
        $startDate = Carbon::now()->subDays( $lookback )->toDateString();
        $endDate = Carbon::now()->toDateString();

        if ( $this->option( 'startDate' ) && $this->option( 'endDate' ) ) {
            $startDate = Carbon::parse( $this->option( 'startDate' ) )->toDateString();
            $endDate = Carbon::parse( $this->option( 'endDate' ) )->toDateString();
        }

        $this->dispatch( new UpdateFeedCountJob( $startDate , $endDate , str_random( 16 ) ) );
    }
}
