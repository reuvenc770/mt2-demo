<?php

namespace App\Listeners;

use App\Events\AttributionCompleted;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use Illuminate\Foundation\Bus\DispatchesJobs;
use App\Jobs\AttributionAggregatorJob;
use Carbon\Carbon;

class RerunAttributionAggregator
{
    use DispatchesJobs;

    const DEFAULT_QUEUE_NAME = 'modelAttribution';
    const DEFAULT_REPORT_TYPE = 'Feed';

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  AttributionCompleted  $event
     * @return void
     */
    public function handle(AttributionCompleted $event)
    {
        $today = Carbon::today();

        for ( $currentDay = 1 ; $currentDay <= $today->day ; $currentDay++ ) {
            $job = ( new AttributionAggregatorJob(
                self::DEFAULT_REPORT_TYPE , 
                str_random( 16 ) ,
                [
                    "start" => Carbon::today()->subDays( $today->day - $currentDay )->startOfDay()->toDateString() ,
                    "end" => Carbon::today()->subDays( $today->day - $currentDay )->endOfDay()->toDateString()
                ] ,
                $event->getModelId()          
            ) )->onQueue( self::DEFAULT_QUEUE_NAME );

            $this->dispatch( $job );
        }
    }
}
