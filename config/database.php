<?php

return [

    /*
    |--------------------------------------------------------------------------
    | PDO Fetch Style
    |--------------------------------------------------------------------------
    |
    | By default, database results will be returned as instances of the PHP
    | stdClass object; however, you may desire to retrieve records in an
    | array format for simplicity. Here you can tweak the fetch style.
    |
    */

    'fetch' => PDO::FETCH_CLASS,

    /*
    |--------------------------------------------------------------------------
    | Default Database Connection Name
    |--------------------------------------------------------------------------
    |
    | Here you may specify which of the database connections below you wish
    | to use as your default connection for all database work. Of course
    | you may use many connections at once using the Database library.
    |
    */

    'default' => env('DB_CONNECTION', 'mysql'),

    /*
    |--------------------------------------------------------------------------
    | Database Connections
    |--------------------------------------------------------------------------
    |
    | Here are each of the database connections setup for your application.
    | Of course, examples of configuring each database platform that is
    | supported by Laravel is shown below to make development simple.
    |
    |
    | All database work in Laravel is done through the PHP PDO facilities
    | so make sure you have the driver for your particular database of
    | choice installed on your machine before you begin development.
    |
    */

    'connections' => [

        'mysql' => [
            'driver'    => 'mysql',
            'host'      => env('DB_HOST', 'localhost'),
            'database'  => env('DB_DATABASE', 'forge'),
            'username'  => env('DB_USERNAME', 'forge'),
            'password'  => env('DB_PASSWORD', ''),
            'charset'   => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix'    => '',
            'strict'    => false,
            'options'   => [PDO::MYSQL_ATTR_LOCAL_INFILE => true],
        ],
        'reporting_data' => [
            'driver'    => 'mysql',
            'host'      => env('DB_HOST', 'localhost'),
            'database'  => env('REPORTS_DB_DATABASE' , '' ), #Not using a default since we have multiple envs
            'username'  => env('DB_USERNAME', 'forge'),
            'password'  => env('DB_PASSWORD', ''),
            'charset'   => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix'    => '',
            'strict'    => false,
        ],
        'mt1supp' => [
            'driver'    => 'mysql',
            'host'      => env('MT1_SUPP_DB_HOST', ''),
            'database'  => env('MT1_SUPP_DB_DATABASE', ''),
            'username'  => env('MT1_SUPP_DB_USERNAME', ''),
            'password'  => env('MT1_SUPP_DB_PASSWORD', ''),
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
            'strict'    => false,
        ],
        'mt1mail' => [
            'driver'    => 'mysql',
            'host'      => env('MT1_MAIL_DB_HOST', ''),
            'database'  => env('MT1_MAIL_DB_DATABASE', ''),
            'username'  => env('MT1_MAIL_DB_USERNAME', ''),
            'password'  => env('MT1_MAIL_DB_PASSWORD', ''),
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
            'strict'    => false,
        ],
        'mt1_table_sync' => [
            'driver'    => 'mysql',
            'host'      => env('MT1_MASTER_DB_HOST', ''),
            'database'  => env('MT1_MASTER_DB_DATABASE', ''),
            'username'  => env('MT1_MASTER_DB_USERNAME', ''),
            'password'  => env('MT1_MASTER_DB_PASSWORD', ''),
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
            'strict'    => false,
        ],
        'mt1_data' => [
            'driver'    => 'mysql',
            'host'      => env('MT1_SLAVE_DB_HOST', ''),
            'database'  => env('MT1_SLAVE_DB_DATABASE', ''),
            'username'  => env('MT1_SLAVE_DB_USERNAME', ''),
            'password'  => env('MT1_SLAVE_DB_PASSWORD', ''),
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
            'strict'    => false,
        ],
        'attribution' => [
            'driver'    => 'mysql',
            'host'      => env('DB_HOST', 'localhost'),
            'database'  => env('ATTR_DB_DATABASE', 'attribution' ), #Not using a default since we have multiple envs
            'username'  => env('DB_USERNAME', 'forge'),
            'password'  => env('DB_PASSWORD', ''),
            'charset'   => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix'    => '',
            'strict'    => false,
        ],

        // Read-only handles from slave db

        'slave_data' => [
            'driver'    => 'mysql',
            #'host'      => env('DB_SLAVE_HOST', 'localhost'),
            'host'      => env('DB_HOST', 'localhost'),
            'database'  => env('DB_DATABASE', '' ), #Not using a default since we have multiple envs
            'username'  => env('DB_SLAVE_USERNAME', 'forge'),
            'password'  => env('DB_SLAVE_PASSWORD', ''),
            'charset'   => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix'    => '',
            'strict'    => false,
        ],

        'slave_reports' => [
            'driver'    => 'mysql',
            #'host'      => env('DB_SLAVE_HOST', 'localhost'),
            'host'      => env('DB_HOST', 'localhost'),
            'database'  => env('REPORTS_DB_DATABASE', 'attribution' ), #Not using a default since we have multiple envs
            'username'  => env('DB_SLAVE_USERNAME', 'forge'),
            'password'  => env('DB_SLAVE_PASSWORD', ''),
            'charset'   => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix'    => '',
            'strict'    => false,
        ],

        'slave_attribution' => [
            'driver'    => 'mysql',
            #'host'      => env('DB_SLAVE_HOST', 'localhost'),
            'host'      => env('DB_HOST', 'localhost'),
            'database'  => env('ATTR_DB_DATABASE', 'attribution' ), #Not using a default since we have multiple envs
            'username'  => env('DB_SLAVE_USERNAME', 'forge'),
            'password'  => env('DB_SLAVE_PASSWORD', ''),
            'charset'   => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix'    => '',
            'strict'    => false,
        ],

        'list_profile' => [
            'driver'    => 'mysql',
            'host'      => env('DB_HOST', 'localhost'),
            'database'  => env('LIST_PROFILE_SCHEMA', 'list_profile' ),
            'username'  => env('DB_USERNAME', 'forge'),
            'password'  => env('DB_PASSWORD', ''),
            'charset'   => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix'    => '',
            'strict'    => false,
        ],

        'list_profile_export_tables' => [
            'driver'    => 'mysql',
            'host'      => env('DB_HOST', 'localhost'),
            'database'  => env('LIST_PROFILE_EXPORT_TABLE_SCHEMA', 'list_profile_export_tables' ),
            'username'  => env('DB_USERNAME', 'forge'),
            'password'  => env('DB_PASSWORD', ''),
            'charset'   => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix'    => '',
            'strict'    => false,
        ],

        'suppression' => [
            'driver'    => 'mysql',
            'host'      => env('DB_HOST', 'localhost'),
            'database'  => env('SUPPRESSION_SCHEMA', '' ),
            'username'  => env('DB_USERNAME', 'forge'),
            'password'  => env('DB_PASSWORD', ''),
            'charset'   => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix'    => '',
            'strict'    => false,
        ],

        'legacy_data_sync' => [
            'driver'    => 'mysql',
            'host'      => env('MT1_MASTER_LEGACY_SYNC_HOST', ''),
            'database'  => env('MT1_MASTER_LEGACY_SYNC_DB', ''),
            'username'  => env('MT1_MASTER_LEGACY_SYNC_USER', ''),
            'password'  => env('MT1_MASTER_LEGACY_SYNC_PW', ''),
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
            'strict'    => false,
        ],


    ],

    /*
    |--------------------------------------------------------------------------
    | Migration Repository Table
    |--------------------------------------------------------------------------
    |
    | This table keeps track of all the migrations that have already run for
    | your application. Using this information, we can determine which of
    | the migrations on disk haven't actually been run in the database.
    |
    */

    'migrations' => 'migrations',

    /*
    |--------------------------------------------------------------------------
    | Redis Databases
    |--------------------------------------------------------------------------
    |
    | Redis is an open source, fast, and advanced key-value store that also
    | provides a richer set of commands than a typical key-value systems
    | such as APC or Memcached. Laravel makes it easy to dig right in.
    |
    */

    'redis' => [

        'cluster' => false,

        'default' => [
            'host'     => env('REDIS_HOST', 'localhost'),
            'password' => env('REDIS_PASSWORD', null),
            'port'     => env('REDIS_PORT', 6379),
            'database' => 0,
        ],

    ],

];
