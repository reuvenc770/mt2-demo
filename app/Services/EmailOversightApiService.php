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

        if ( in_array( $response->ResultId , $this->validCodes ) ) {
            return true;
        }

        $this->suppressEmail( $email , $response );

        return false;
    }

    protected function suppressEmail ( $email , $validationResponse ) {
        $date = Carbon::today()->toDateString();
        $reasonId = 0;

        if ( isset( $this->invalidCodeSuppTypeMap[ $validationResponse->ResultId ] ) ) {
            $reasonId = $this->invalidCodeSuppTypeMap[ $validationResponse->ResultId ];
        }

        Suppression::recordSuppressionByReason( $email , $date , $reasonId );
        Suppression::recordGlobalSuppressionByReason( $email , $date , $reasonId );
    }
}
