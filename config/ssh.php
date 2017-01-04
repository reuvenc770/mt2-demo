<?php

return [
    'servers' => [
        'mt1_slave_db_server' => [
            'host'      => env('MT1_SLAVE_DB3_HOST', 'localhost'),
            'username'  => env('MT1_SLAVE_DB3_USER', 'forge'),
            'password'  => env('MT1_SLAVE_DB3_PASS', ''),
            'port'      => env('MT1_SLAVE_DB3_PORT', '')
        ],

        'mt1_feed_file_server' => [
            'host'      => env('FEED_FILE_HOST', 'localhost'),
            'username'  => env('FEED_FILE_USER', 'forge'),
            'port'      => env('FEED_FILE_PORT', ''),
            'public_key' => env('FEED_FILE_PUB_KEY', ''),
            'private_key' => env('FEED_FILE_PRIV_KEY', ''),
        ],

        'mt1_file_upload' => [
            'host' => env('MT1_FILE_UPLOAD_HOST', ''),
            'username' => env('MT1_FILE_UPLOAD_USER', ''),
            'password' => env('MT1_FILE_UPLOAD_PASS', ''),
            'port' => env('MT1_FILE_UPLOAD_PORT', ''),
            'remote_dir' => env('MT1_FILE_UPLOAD_DIRECTORY', '')
        ],
    ]
    
];