<?php

namespace App\Listeners;

use App\Events\AttributionCompleted;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use Illuminate\Foundation\Bus\DispatchesJobs;
use App\Jobs\AttributionAggregatorJob;
use Carbon\Carbon;
use Mail;

use App\Services\AttributionModelService;

class RerunAttributionAggregator
{
    use DispatchesJobs;

    const DEFAULT_QUEUE_NAME = 'default'; #'modelAttribution';
    const DEFAULT_REPORT_TYPE = 'Feed';

    private $attrService;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct( AttributionModelService $attrService )
    {
        $this->attrService = $attrService;
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

        $this->attrService->setProcessingFlag( $event->getModelId() , false );

        $userEmail = $event->getUserEmail()
        if ( !is_null( $userEmail ) && $userEmail != 'none' ) {
            Mail::raw( 'Projection processing for Model' . $event->getModelId() . ' completed.' , function ($message) {
                $message->to( $userEmail );
                $message->to('achin@zetainteractive.com');
                $message->subject('"Projection Processing Completed"');
                $message->priority(1);
            });
        }
    }
}
