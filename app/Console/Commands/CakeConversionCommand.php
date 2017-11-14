<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Carbon\Carbon;
use App\Jobs\AttributionConversionJob;

class CakeConversionCommand extends Command
{
    use DispatchesJobs;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reports:downloadCakeConversions {--d|daysBack= : How long ago to pull stats for. } {--s|startDate= : The starting date for processing attribution revenue. } {--e|endDate= : The end date for processing attribution revenue. }';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command will fire off a job to download CAKE conversions for the given date range.';

    protected $dateRange;

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
        $this->processOptions();

        $this->dispatch( new AttributionConversionJob(
            str_random( 16 ) ,
            $this->dateRange
        ) );
    }

    protected function processOptions () {
        if ( !is_null( $this->option( 'daysBack' ) ) ) {
            $this->dateRange = [
                'start' => Carbon::now()->subDays( $this->option( 'daysBack' ) )->toDateString() ,
                'end' => Carbon::now()->toDateString()
            ];
            
        } else {
            if ( $this->isInvalidDateRange( $this->option( 'startDate' ) , $this->option( 'endDate' ) ) ) {
                $this->error( "Invalid date range: " . json_encode( [ 'startDate' => $this->option( 'startDate' ) , 'endDate' => $this->option( 'endDate' ) ] ) );
                exit();
            }

            $this->dateRange = [
                'start' => Carbon::parse( $this->option( 'startDate' ) )->toDateString() ,
                'end' => Carbon::parse( $this->option( 'endDate' ) )->toDateString()
            ];
        }
    }

    protected function isInvalidDateRange ( $startDate , $endDate ) {
        if ( is_null( $startDate ) || is_null( $endDate ) ) {
            return true;
        }

        try {
            #Check if end date is before the start date
            if ( Carbon::parse( $startDate )->diffInDays( Carbon::parse( $endDate ) , false ) < 0 ) {
                return true;
            }
        } catch ( \Exception $e ) {
            return true;
        }

        return false;
    }
}
