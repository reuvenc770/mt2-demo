<?php

namespace App\Repositories\MT1Repositories;

use DB;
use Log;

class ClientAttributionRepo {
    const DELETED_LEVEL = 255;

    public function __construct () {} 

    public function getClientsByAttribution ( $count ) {
        $clients = DB::connection( 'mt1mail' )->table( 'user' )
            ->select( 'user_id as id' , 'username as name' , 'AttributeLevel as level' , 'countryCode as country' , 'company' )
            ->join( 'Country' , 'user.countryID' , '=' , 'Country.countryID' )
            ->where( [
                [ 'user.OrangeClient' , '=' , 'Y' ] ,
                [ 'user.status' , '=' , 'A'  ] ,
                [ 'user.AttributeLevel' , '!=' , '255' ]
           ] )
           ->orderBy( 'AttributeLevel' )
           ->paginate( $count );

        return $clients;
    }

    public function setAttribution ( $id , $level ) {
        Log::info( "Setting Client ID: {$id} - Level: {$level}" );

        /* DB Handle to adjust attribution level. Need to write to master, current handle is slave.
        DB::connection( 'mt1mail' )->table( 'user' )
            ->where( 'user_id' , '=' , $id )
            ->update( [ 'AttributeLevel' => $level ] );
         */
    }

    public function deleteAttribution ( $id ) {
        Log::info( "Deleting Client ID: {$id}" );

        /* DB Handle to adjust remove attribution level by switching to 255 which is disabled throughout the attribution script.. Need to write to master, current handle is slave.
        DB::connection( 'mt1mail' )->table( 'user' )
            ->where( 'user_id' , '=' , $id )
            ->update( [ 'AttributeLevel' => self::DELETED_LEVEL ] );
         */
    }
}
