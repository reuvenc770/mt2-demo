<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

$factory->define( App\Models\Feed::class , function ( Faker\Generator $faker ) {
    $companyName = $faker->company;

    return [
        'name' => $companyName ,
        'party' => 3 ,
        'short_name' => strtoupper( "{$companyName[0]}{$companyName[2]}" . $companyName[ strlen( $companyName ) - 1 ] ) ,
        'password' => $faker->password( 8 , 8 ) ,
        'host_ip' => 'sftp-01.mtroute.com' ,
        'status' => 'Active'
    ];
} );
