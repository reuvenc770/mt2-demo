<?php

namespace App\Repositories\MT1Repositories;

use DB;

class ClientAttributionRepo {
    public function __construct () {} 

    public function getFeedsByAttribution ( $count ) {
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
}
