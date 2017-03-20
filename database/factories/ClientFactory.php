<?php
/**
 * @author Adam Chin <achin@zetaglobal.com>
 */

$factory->define( App\Models\Client::class , function ( Faker\Generator $faker ) {
    return [
        'name' => $faker->company ,
        'address' => $faker->streetAddress ,
        'city' => $faker->city ,
        'state' => $faker->state ,
        'zip' => $faker->postcode ,
        'email_address' => $faker->email ,
        'phone' => $faker->phoneNumber ,
        'status' => 'Active'
    ];
} );
