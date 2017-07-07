<?php
/**
 * @author Adam Chin <achin@zetaglobal.com>
 */

$factory->define( App\Models\EspAccount::class , function ( Faker\Generator $faker ) {
    return [
        'id' => $faker->randomDigit ,
        'name' => $faker->company ,
        'account_name' => $faker->company ,
        'enable_suppression' => 1
    ];
} );
