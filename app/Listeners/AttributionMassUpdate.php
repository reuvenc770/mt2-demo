<?php

namespace App\Listeners;

use App\Events\AttributionFileUploaded;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Services\MT1Services\ClientAttributionService;
use Storage;
use Log;

class AttributionMassUpdate
{
    protected $service;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct( ClientAttributionService $service )
    {
        $this->service = $service;
    }

    /**
     * Handle the event.
     *
     * @param  AttributionFileUploaded  $event
     * @return void
     */
    public function handle(AttributionFileUploaded $event)
    {
        $contents = Storage::get( $event->getFilePath() );

        $lines = explode( PHP_EOL , $contents );

        foreach ( $lines as $clientAdjustment ) {
            list( $level , $clientId ) = explode( ',' , $clientAdjustment );

            $this->service->setAttribution( $level , $clientId );
        }
    }
}
