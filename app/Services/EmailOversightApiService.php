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
    protected $invalidCodeSuppTypeMap = [
        2 => 56 ,
        3 => 57 ,
        4 => 58 ,
        5 => 59 ,
        6 => 60 ,
        7 => 61 ,
        9 => 62 ,
        10 => 63 ,
        13 => 64 ,
        20 => 65
    ];

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
        $reasonId = 0;

        if ( isset( $this->invalidCodeSuppTypeMap[ $validationResponse->ResultId ] ) ) {
            $reasonId = Suppression::getReasonIdFromSubstring( 'Oversight - ' . $validationResponse->Result );
        }

        Suppression::recordSuppressionByReason( $email , $date , $reasonId );
        Suppression::recordGlobalSuppressionByReason( $email , $date , $reasonId );
    }
}
