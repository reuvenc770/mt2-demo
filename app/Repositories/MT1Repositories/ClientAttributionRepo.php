<?php

namespace App\Repositories\MT1Repositories;

use DB;
use Log;

class ClientAttributionRepo {
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

        #Need to decide what to do from here
    }

    public function deleteAttribution ( $id ) {
        Log::info( "Deleting Client ID: {$id}" );

        #Need to decide what to do from here
    }
}
