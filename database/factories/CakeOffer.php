<?php
/**
 * @author Adam Chin <achin@zetaglobal.com>
 */

$factory->define( App\Models\CakeOffer::class , function ( Faker\Generator $faker ) {
    return [ 
        'name' => $faker->company ,
        'vertical_id' => $faker->randomDigit ,
        'cake_advertiser_id' => $faker->randomDigit
    ];
} );
