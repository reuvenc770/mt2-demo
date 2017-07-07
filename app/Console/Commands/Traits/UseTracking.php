<?php
/**
 * @author Adam Chin <achin@zetaglobal.com>
 */

namespace App\Console\Commands\Traits;

trait UseTracking {
    protected $minTrackingIdLength = 16;
    protected $trackingIdLength = 16;

    protected $trackingId;

    public function getTrackingId () {
        if ( !isset( $this->trackingId ) ) {
            $this->generateTrackingId();
        }

        return $this->trackingId;
    }

    public function setTrackingId ( $trackingId ) {
        if ( strlen( $trackingId ) < $this->minTrackingIdLength ) {
            throw new \Exception( 'Tracking ID must be ' . $this->minTrackingIdLength . ' characters or larger.' );
        }

        $this->trackingId = $trackingId;
    }

    public function generateTrackingId () {
        $this->trackingId =  str_random( $this->trackingIdLength );
    }
}
