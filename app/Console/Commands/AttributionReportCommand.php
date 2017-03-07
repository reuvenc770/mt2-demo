<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Carbon\Carbon;

use App\Jobs\AttributionConversionJob;
use App\Jobs\AttributionAggregatorJob;
use App\Services\OfferPayoutService;
use App\Services\AttributionAggregatorService;

class AttributionReportCommand extends Command
{
    use DispatchesJobs;

    const RUN_ALL = 'all';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'attribution:reporting {--d|daysBack= : How long ago to pull stats for. } {--s|startDate= : The starting date for processing attribution revenue. } {--e|endDate= : The end date for processing attribution revenue. } {--M|modelId= : The model ID to run attribution for. } {--N|notificationChannel=mt2team : The slack channel to ping when processing is done. }';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command will fire off jobs to grab recent conversion data and process CPA/CPM attribution.';

    protected $processMode;
    protected $dateRange;
    protected $modelId;
    protected $slackChannel;

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
    public function handle( OfferPayoutService $payout )
    {
        $this->processOptions();

        $this->dispatch( new AttributionConversionJob(
            str_random( 16 ) ,
            $this->dateRange ,
            $this->modelId
        ) );

        foreach ( $payout->getCpmOffers() as $currentOfferId ) {
            $this->dispatch( new AttributionAggregatorJob(
                AttributionAggregatorService::RUN_CPM ,
                $this->dateRange ,
                str_random( 16 ) ,
                $currentOfferId ,
                $this->modelId
            ) );
        }
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
        
        $this->modelId = $this->option( 'modelId' );

        $this->slackChannel = $this->option( 'notificationChannel' );
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
