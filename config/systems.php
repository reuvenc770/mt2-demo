<?php
/**
 * configuration for specific MT2 platform instances
 */

return [

    'local' => [
        'host' => 'localhost',
        'name' => 'Development Sandbox',
        'privateIP' => '127.0.0.1'
    ],

    'staging' => [
        'host' => env( 'RUNTIME_STG_HOST' ) ,
        'name' => env( 'RUNTIME_STG_NAME' ),
        'privateIP' => env( 'RUNTIME_STG_PRV_IP' )
    ],

    'production' => [
        'host' => env( 'RUNTIME_PRD_HOST' ) ,
        'name' => env( 'RUNTIME_PRD_NAME' ),
        'privateIP' => env( 'RUNTIME_PRD_PRV_IP' )
    ]
];
