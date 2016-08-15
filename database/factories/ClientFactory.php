<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

$factory->define( App\Models\Feed::class , function ( Faker\Generator $faker ) {
    return [
        'name' => $faker->name . ' LLC' ,
        'status' => 'Active'
    ];
} );
