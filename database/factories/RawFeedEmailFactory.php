<?php
/**
 * @author Adam Chin <achin@zetaglobal.com>
 */

$factory->define( App\Models\RawFeedEmail::class , function ( Faker\Generator $faker ) {
    return [
        'feed_id' => $faker->randomNumber() ,
        'party' => $faker->randomElement( [ 1 , 3 ] ) ,
        'realtime' => $faker->boolean() ,
        'email_address' => $faker->email , 
        'source_url' => $faker->url ,
        'capture_date' => \Carbon\Carbon::instance( $faker->dateTime() )->toDatetimeString() ,
        'ip' =>$faker->ipv4 , #$faker->randomElement( [ $faker->ipv4 , $faker->ipv6 ] ) ,
        'first_name' => $faker->firstName ,
        'last_name' => $faker->lastName ,
        'address' => $faker->streetAddress ,
        'address2' => $faker->randomElement( [ '' , $faker->secondaryAddress ] ) ,
        'city' => $faker->city ,
        'state' => $faker->stateAbbr ,
        'zip' => $faker->postcode ,
        'country' => $faker->randomElement( [ 'US' , 'UK' ] ) ,
        'gender' => $faker->randomElement( [ 'M' , 'F' ] ) ,
        'phone' => $faker->phoneNumber ,
        'dob' => $faker->date( 'Y-m-d' , '-18 years' ) ,
        'other_fields' => '{}' ,
        'file' => '/home/somebody/file.csv' ,
        'created_at' => \Carbon\Carbon::instance( $faker->dateTime() )->toDatetimeString() ,
        'updated_at' => \Carbon\Carbon::instance( $faker->dateTime() )->toDatetimeString()
    ];
} );
