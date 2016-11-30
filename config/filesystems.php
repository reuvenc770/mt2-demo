<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default filesystem disk that should be used
    | by the framework. A "local" driver, as well as a variety of cloud
    | based drivers are available for your choosing. Just store away!
    |
    | Supported: "local", "ftp", "s3", "rackspace"
    |
    */

    'default' => 'local',

    /*
    |--------------------------------------------------------------------------
    | Default Cloud Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Many applications store files both locally and in the cloud. For this
    | reason, you may specify a default "cloud" driver here. This driver
    | will be bound as the Cloud disk implementation in the container.
    |
    */

    'cloud' => 's3',

    /*
    |--------------------------------------------------------------------------
    | Filesystem Disks
    |--------------------------------------------------------------------------
    |
    | Here you may configure as many filesystem "disks" as you wish, and you
    | may even configure multiple disks of the same driver. Defaults have
    | been setup for each driver as an example of the required options.
    |
    */

    'disks' => [

        'local' => [
            'driver' => 'local',
            'root'   => storage_path('app'),
        ],

        'sprintUnsubCampaignFTP' => [
            'driver'   => 'ftp',
            'host'     => env( 'SPRINT_CAMPAIGN_FTP_HOST' ) , 
            'username' => env( 'SPRINT_CAMPAIGN_FTP_USER' ) ,
            'password' => env( 'SPRINT_CAMPAIGN_FTP_PASS' ) ,
        ],

        'hornet7' => [
            'driver'   => 'ftp',
            'host'     => env("HORNET7_IP"),
            'username' => env("HORNET7_USERNAME"),
            'password' => env("HORNET7_PW"),
        ],

        'dataExportFTP' => [
            'driver'    => 'ftp' ,
            'host'     => env( 'DATAEXPORT_FTP_HOST' ),
            'username' => env( 'DATAEXPORT_FTP_USER' ),
            'password' => env( 'DATAEXPORT_FTP_PASS' ),
        ] ,

        'sprintUnsubFTP' => [
            'driver'   => 'ftp',
            'host'     => env( 'SPRINT_UNSUB_FTP_HOST' ),
            'username' => env( 'SPRINT_UNSUB_FTP_USER' ),
            'password' => env( 'SPRINT_UNSUB_FTP_PASS' ),
        ] ,
        'MT1SuppressionDropOff' => [
            'driver'   => 'ftp',
            'host'     => env( 'MT1_SUPPRESSION_DROPOFF_HOST' ),
            'username' => env( 'MT1_SUPPRESSION_DROPOFF_USER' ),
            'password' => env( 'MT1_SUPPRESSION_DROPOFF_PASSWORD' ),
        ] ,

        'ZxUnsubFtp' => [
            'driver' => 'sftp',
            'host' => env('ZX_FTP_HOST'),
            'username' => env('ZX_FTP_USER'),
            'password' => env('ZX_FTP_PASSWORD')
        ],
        
        'SystemFtp' => [
            'driver' => 'ftp',
            'host' => env('MAIN_SYSTEM_FTP_HOST'),
            'username' => env('MAIN_SYSTEM_FTP_USER'),
            'password' => env('MAIN_SYSTEM_FTP_PASS')
        ],

        'espdata' => [
            'driver' => 'ftp',
            'host'     => env( 'ESPDATA_FTP_HOST' ),
            'username' => env( 'ESPDATA_FTP_USER' ),
            'password' => env( 'ESPDATA_FTP_PASS' ),
        ],

        'ftp' => [
            'driver'   => 'ftp',
            'host'     => 'ftp.example.com',
            'username' => 'your-username',
            'password' => 'your-password',

            // Optional FTP Settings...
            // 'port'     => 21,
            // 'root'     => '',
            // 'passive'  => true,
            // 'ssl'      => true,
            // 'timeout'  => 30,
        ],

        's3' => [
            'driver' => 's3',
            'key'    => 'your-key',
            'secret' => 'your-secret',
            'region' => 'your-region',
            'bucket' => 'your-bucket',
        ],

        'rackspace' => [
            'driver'    => 'rackspace',
            'username'  => 'your-username',
            'key'       => 'your-key',
            'container' => 'your-container',
            'endpoint'  => 'https://identity.api.rackspacecloud.com/v2.0/',
            'region'    => 'IAD',
            'url_type'  => 'publicURL',
        ],

    ],

];
