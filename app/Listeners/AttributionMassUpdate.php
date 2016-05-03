<?php

namespace App\Listeners;

use App\Events\AttributionFileUploaded;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Services\MT1ApiService;
use Storage;
use Log;

class AttributionMassUpdate
{
    const ATTRIBUTION_UPLOAD_ENDPOINT = "attribution_update";

    protected $service;
    protected $attributionApi;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct( MT1ApiService $service , ClientAttributionService $attrService )
    {
        $this->service = $service;
        $this->attributionApi = $attrService;
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
            $adjustment = explode( ',' , $clientAdjustment );

            if (
                count( $adjustment ) == 2
                && is_numeric( $adjustment[ 0 ] )
                && is_numeric( $adjustment[ 1 ] )
            ) {
                $this->service->postForm(
                    self::ATTRIBUTION_UPLOAD_ENDPOINT ,
                    [ 'level' => $adjustment[ 1 ] , 'cid' => $adjustment[ 0 ] ]
                );
            }
        }

        $this->attributionApi->flushCache();
    }
}
