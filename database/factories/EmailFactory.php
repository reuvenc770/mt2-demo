<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

$factory->define( App\Models\Email::class , function ( Faker\Generator $faker ) {
    return [
        'email_address' => $faker->email
    ];
} );
