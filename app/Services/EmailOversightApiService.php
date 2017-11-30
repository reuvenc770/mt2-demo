<?php

namespace App\Services;

use App\Services\API\EmailOversightApi;
use App\Repositories\EmailOversightValidCacheRepo;
use App\Facades\Suppression;
use Carbon\Carbon;

class EmailOversightApiService {
    protected $api;
    protected $cache;

    protected $validCodes = [ 1 , 11 ];
    protected $lastResultMessage;

    public function __construct ( EmailOversightApi $api , EmailOversightValidCacheRepo $cache ) {
        $this->api = $api;
        $this->cache = $cache;
    }

    public function verifyEmail ( $listId , $email ) {
        if ( $this->cache->emailExists( $email ) ) {
            return true;
        }

        /**
         * The record should not be previously suppressed; waste of credits.
         */
        $response = $this->api->verifyEmail( $listId , $email );

        $this->lastResultMessage = $response->Result;

        if ( in_array( $response->ResultId , $this->validCodes ) ) {
            $this->cache->cacheEmail( $email );

            return true;
        }

        $this->suppressEmail( $email , $response );

        return false;
    }

    public function getLastMessage () {
        return $this->lastResultMessage;
    }

    protected function suppressEmail ( $email , $validationResponse ) {
        $date = Carbon::today()->toDateString();
        $reasonId = Suppression::getReasonIdFromSubstring( 'Oversight - ' . $validationResponse->Result );

        Suppression::recordSuppressionByReason( $email , $date , $reasonId );
        Suppression::recordGlobalSuppressionByReason( $email , $date , $reasonId );
    }
}
