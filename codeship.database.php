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
            'database'  => env('TEST_ENV_NUMBER'),
            'username'  => env('MYSQL_USER'),
            'password'  => env('MYSQL_PASSWORD'),
            'charset'   => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix'    => '',
            'strict'    => false,
            'options'   => [PDO::MYSQL_ATTR_LOCAL_INFILE => true],
        ],
        'reporting_data' => [
            'driver'    => 'mysql',
            'host'      => env('DB_HOST', 'localhost'),
            'database'  => env('TEST_ENV_NUMBER'), #Not using a default since we have multiple envs
            'username'  => env('MYSQL_USER'),
            'password'  => env('MYSQL_PASSWORD'),
            'charset'   => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix'    => '',
            'strict'    => false,
        ],
        'dima_data' => [
            'driver'    => 'mysql',
            'host'      => env('DB_HOST', 'localhost'),
            'database'  => env('TEST_ENV_NUMBER'),
            'username'  => env('MYSQL_USER'),
            'password'  => env('MYSQL_PASSWORD'),
            'charset'   => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix'    => '',
            'strict'    => false,
        ],
        'mt1supp' => [
            'driver'    => 'mysql',
            'host'      => env('DB_HOST', 'localhost'),
            'database'  => env('TEST_ENV_NUMBER'),
            'username'  => env('MYSQL_USER'),
            'password'  => env('MYSQL_PASSWORD'),
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
            'strict'    => false,
        ],
        'mt1mail' => [
            'driver'    => 'mysql',
            'host'      => env('DB_HOST', 'localhost'),
            'database'  => env('TEST_ENV_NUMBER'),
            'username'  => env('MYSQL_USER'),
            'password'  => env('MYSQL_PASSWORD'),
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
            'strict'    => false,
        ],
        'mt1_table_sync' => [
            'driver'    => 'mysql',
            'host'      => env('DB_HOST', 'localhost'),
            'database'  => env('TEST_ENV_NUMBER'),
            'username'  => env('MYSQL_USER'),
            'password'  => env('MYSQL_PASSWORD'),
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
            'strict'    => false,
        ],
        'mt1_data' => [
            'driver'    => 'mysql',
            'host'      => env('DB_HOST', 'localhost'),
            'database'  => env('TEST_ENV_NUMBER'),
            'username'  => env('MYSQL_USER'),
            'password'  => env('MYSQL_PASSWORD'),
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
            'strict'    => false,
        ],
        'attribution' => [
            'driver'    => 'mysql',
            'host'      => env('DB_HOST', 'localhost'),
            'database'  => env('TEST_ENV_NUMBER'), #Not using a default since we have multiple envs
            'username'  => env('MYSQL_USER'),
            'password'  => env('MYSQL_PASSWORD'),
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
            'database'  => env('TEST_ENV_NUMBER'), #Not using a default since we have multiple envs
            'username'  => env('MYSQL_USER'),
            'password'  => env('MYSQL_PASSWORD'),
            'charset'   => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix'    => '',
            'strict'    => false,
        ],

        'slave_reports' => [
            'driver'    => 'mysql',
            #'host'      => env('DB_SLAVE_HOST', 'localhost'),
            'host'      => env('DB_HOST', 'localhost'),
            'database'  => env('TEST_ENV_NUMBER'), #Not using a default since we have multiple envs
            'username'  => env('MYSQL_USER'),
            'password'  => env('MYSQL_PASSWORD'),
            'charset'   => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix'    => '',
            'strict'    => false,
        ],

        'slave_attribution' => [
            'driver'    => 'mysql',
            #'host'      => env('DB_SLAVE_HOST', 'localhost'),
            'host'      => env('DB_HOST', 'localhost'),
            'database'  => env('TEST_ENV_NUMBER'), #Not using a default since we have multiple envs
            'username'  => env('MYSQL_USER'),
            'password'  => env('MYSQL_PASSWORD'),
            'charset'   => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix'    => '',
            'strict'    => false,
        ],

        'list_profile' => [
            'driver'    => 'mysql',
            'host'      => env('DB_HOST', 'localhost'),
            'database'  => env('TEST_ENV_NUMBER'),
            'username'  => env('MYSQL_USER'),
            'password'  => env('MYSQL_PASSWORD'),
            'charset'   => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix'    => '',
            'strict'    => false,
        ],

        'list_profile_export_tables' => [
            'driver'    => 'mysql',
            'host'      => env('DB_HOST', 'localhost'),
            'database'  => env('TEST_ENV_NUMBER'),
            'username'  => env('MYSQL_USER'),
            'password'  => env('MYSQL_PASSWORD'),
            'charset'   => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix'    => '',
            'strict'    => false,
        ],

        'suppression' => [
            'driver'    => 'mysql',
            'host'      => env('DB_HOST', 'localhost'),
            'database'  => env('TEST_ENV_NUMBER'),
            'username'  => env('MYSQL_USER'),
            'password'  => env('MYSQL_PASSWORD'),
            'charset'   => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix'    => '',
            'strict'    => false,
        ],

        'legacy_data_sync' => [
            'driver'    => 'mysql',
            'host'      => env('DB_HOST', 'localhost'),
            'database'  => env('TEST_ENV_NUMBER'),
            'username'  => env('MYSQL_USER'),
            'password'  => env('MYSQL_PASSWORD'),
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
            'strict'    => false,
        ],
//update later with psql lite driver or something
        'redshift' => [
            'driver' => 'pgsql',
            'host'      => env('REDSHIFT_HOST', ''),
            'database'  => env('TEST_ENV_NUMBER'),
            'username'  => env('MYSQL_USER'),
            'password'  => env('MYSQL_PASSWORD'),
            'port' => env('REDSHIFT_PORT', ''),
            'charset' => 'utf8'
        ]

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

        'cache' => [
            'host'     => env('REDIS_CACHE_HOST', 'localhost'),
            'password' => env('REDIS_CACHE_PASSWORD', null),
            'port'     => env('REDIS_CACHE_PORT', 6379),
            'database' => 0,
        ],
        'queue' => [
            'host'     => env('REDIS_QUEUE_HOST', 'localhost'),
            'password' => env('REDIS_QUEUE_PASSWORD', null),
            'port'     => env('REDIS_QUEUE_PORT', 6379),
            'database' => 0,
        ],
        'sessions' => [
            'host'     => env('REDIS_SESSIONS_HOST', 'localhost'),
            'password' => env('REDIS_SESSIONS_PASSWORD', null),
            'port'     => env('REDIS_SESSIONS_PORT', 6379),
            'database' => 0,
        ],
        'thirdparty' => [
            'host'     => env('REDIS_THIRD_HOST', 'localhost'),
            'password' => env('REDIS_THIRD_PASSWORD', null),
            'port'     => env('REDIS_THIRD_PORT', 6379),
            'database' => 0,
        ],


    ],

];
