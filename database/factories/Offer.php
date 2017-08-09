<?php
/**
 * @author Adam Chin <achin@zetaglobal.com>
 */

$factory->define( App\Models\Offer::class , function ( Faker\Generator $faker ) {
    return [
        'name' => $faker->company ,
        'is_approved' => 1 ,
        'status' => 1 ,
        'advertiser_id' => $faker->randomDigit ,
        'offer_payout_type_id' => $faker->numberBetween( 1 , 5 ) 
    ];
} );
