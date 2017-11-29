<?php
/**
 * @author Adam Chin <achin@zetaglobal.com>
 */

$factory->define( App\Models\Deploy::class , function ( Faker\Generator $faker ) {
    return [
        'deploy_name' => $faker->name  ,
        'send_date' => $faker->date() ,
        'esp_account_id' => $faker->numberBetween( 1 , 40 ) ,
        'external_deploy_id' => $faker->randomNumber() ,
        'offer_id' => $faker->randomNumber() ,
        'creative_id' => $faker->randomNumber() ,
        'from_id' => $faker->randomNumber() ,
        'subject_id' => $faker->randomNumber() ,
        'template_id' => $faker->randomNumber() ,
        'mailing_domain_id' => $faker->randomNumber() ,
        'content_domain_id' => $faker->randomNumber() ,
        'list_profile_combine_id' => $faker->randomNumber() ,
        'cake_affiliate_id' => $faker->randomDigit() ,
        'encrypt_cake' => $faker->boolean() ,
        'fully_encrypt' => $faker->boolean() ,
        'url_format' => $faker->randomElement( [ 'short' , 'long' , 'encrypt' ] ) ,
        'party' => $faker->numberBetween( 1 , 3 ) ,
        'deployment_status' => $faker->boolean()
    ];
} );
