<?php

use Illuminate\Support\Str;

return [

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

    'sqlite' => [
      'driver' => 'sqlite',
      'url' => env('DATABASE_URL'),
      'database' => env('DB_DATABASE', database_path('database.sqlite')),
      'prefix' => '',
      'foreign_key_constraints' => env('DB_FOREIGN_KEYS', true),
    ],

    'mysql' => [
      'driver' => 'mysql',
      'url' => env('DATABASE_URL'),
      'host' => env('DB_HOST', '127.0.0.1'),
      'port' => env('DB_PORT', '3306'),
      'database' => env('DB_DATABASE', 'forge'),
      'username' => env('DB_USERNAME', 'forge'),
      'password' => env('DB_PASSWORD', ''),
      'unix_socket' => env('DB_SOCKET', ''),
      'charset' => 'utf8mb4',
      'collation' => 'utf8mb4_unicode_ci',
      'prefix' => '',
      'prefix_indexes' => true,
      'strict' => true,
      'engine' => null,
      'options' => extension_loaded('pdo_mysql') ? array_filter([
        PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
      ]) : [],
    ],

    'pgsql' => [
      'driver' => 'pgsql',
      'url' => env('DATABASE_URL'),
      'host' => env('DB_HOST', '127.0.0.1'),
      'port' => env('DB_PORT', '5432'),
      'database' => env('DB_DATABASE', 'forge'),
      'username' => env('DB_USERNAME', 'forge'),
      'password' => env('DB_PASSWORD', ''),
      'charset' => 'utf8',
      'prefix' => '',
      'prefix_indexes' => true,
      'schema' => 'public',
      'sslmode' => 'prefer',
    ],

    'pgsql_paud' => [
      'driver' => 'pgsql',
      'url' => env('DATABASE_URL'),
      'host' => env('DB_HOST_PAUD', '127.0.0.1'),
      'port' => env('DB_PORT_PAUD', '5432'),
      'database' => env('DB_DATABASE_PAUD', 'forge'),
      'username' => env('DB_USERNAME_PAUD', 'forge'),
      'password' => env('DB_PASSWORD_PAUD', ''),
      'charset' => 'utf8',
      'prefix' => '',
      'prefix_indexes' => true,
      'schema' => 'public',
      'sslmode' => 'prefer',
    ],

    'sqlsrv' => [
      'driver' => 'sqlsrv',
      'url' => env('DATABASE_URL'),
      'host' => env('DB_HOST', 'localhost'),
      'port' => env('DB_PORT', '1433'),
      'database' => env('DB_DATABASE', 'forge'),
      'username' => env('DB_USERNAME', 'forge'),
      'password' => env('DB_PASSWORD', ''),
      'charset' => 'utf8',
      'prefix' => '',
      'prefix_indexes' => true,
    ],

    'db_paud' => [
      'driver'   => 'pgsql',
      'host'     => '103.76.19.82',
      'port'     => '5432',
      'database' => 'profdikpaud',
      'username' => 'postgres',
      'password' => 'trisula',
      'charset'  => 'utf8',
      'prefix'   => '',
      'schema'   => 'public',
    ],

    'db_sd' => [
      'driver' => 'pgsql',
      'port' => env('DB_PORT_SD', '25432'),
      'host' => env('DB_HOST_SD', '103.76.17.7'),
      'database' => env('DB_DATABASE_SD', 'profdiksd'),
      'username' => env('DB_USERNAME_SD', 'sekolahdasar'),
      'password' => env('DB_PASSWORD_SD', 'j@w4_t!MuR'),
      'charset' => 'utf8',
      'prefix' => '',
      'schema' => 'public',
      'sslmode' => 'prefer',
    ],

    'db_smp' => [
      'driver' => 'pgsql',
      'url' => env('DATABASE_URL'),
      'host' => env('DB_HOST_SMP', '103.76.17.8'),
      'port' => env('DB_PORT_SMP', '55432'),
      'database' => env('DB_DATABASE_SMP', 'profdiksmp'),
      'username' => env('DB_USERNAME_SMP', 'smpdispendik'),
      'password' => env('DB_PASSWORD_SMP', 'Mount@ink@w!'),
      'charset' => 'utf8',
      'prefix' => '',
      'prefix_indexes' => true,
      'schema' => 'public',
      'sslmode' => 'prefer',
    ],

    'db_cutt_off' => [
      'driver' => 'pgsql',
      'port' => env('DB_PORT_CUTOFF', '8483'),
      'host' => env('DB_HOST_CUTOFF', '10.10.20.7'),
      'database' => env('DB_DATABASE_CUTOFF', 'cutoff'),
      'username' => env('DB_USERNAME_CUTOFF', 'sekolahdasar'),
      'password' => env('DB_PASSWORD_CUTOFF', 'j@w4_t!MuR'),
      'charset' => 'utf8',
      'prefix' => '',
      'schema' => 'public',
      'sslmode' => 'prefer',
    ],

    'db_siagus' => [
      'driver' => 'pgsql',
      'port' => '35432',
      'host' => '103.76.19.85',
      'database' => 'agdpks',
      'username' => 'SiaguS',
      'password' => 'GunUn9!der!der',
      'charset' => 'utf8',
      'prefix' => '',
      'schema' => 'public',
      'sslmode' => 'prefer',
    ],

    'db_sinau_bareng' => [
      'driver' => 'pgsql',
      'port' => env('port_opis', '8483'),
      'host' => env('host_opis', '10.10.20.100'),
      'database' => 'sinau_bareng',
      'username' => 'eoffice',
      'password' => 'Gor0nT@L0',
      'charset' => 'utf8',
      'prefix' => '',
      'schema' => 'public',
      'sslmode' => 'prefer',
    ],

    'perijinan' => [
      'driver'   => 'pgsql',
      'host'     => '103.76.17.7',
      'port'     => '15431',
      'database' => 'perijinan',
      'username' => 'perijinan',
      'password' => '$umBaw4',
      'charset'  => 'utf8',
      'prefix'   => '',
      'schema'   => 'public',
    ],

    'db_simbas' => [
      'driver'   => 'pgsql',
      'host'     => '103.76.17.7',
      'port'     => '15431',
      'database' => 'simbas',
      'username' => 'simbas',
      'password' => 'dispendik356',
      'charset'  => 'utf8',
      'prefix'   => '',
      'schema'   => 'public',
    ],

    'db_simpaud' => [
      'driver'   => 'pgsql',
      'host'     => '103.76.17.7',
      // 'host'     => '10.10.20.100',
      'port'     => '15431',
      // 'port'     => '8483',
      'database' => 'simpaud',
      'username' => 'simpaud',
      'password' => 'L@mpuNg',
      'charset'  => 'utf8',
      'prefix'   => '',
      'schema'   => 'public',
    ],

    'db_genmas' => [
      'driver'    => 'mysql',
      'host'      => env('DB_HOST_GENMAS', '103.76.17.7'),
      'port'      => '3306',
      'database'  => 'genmas',
      'username'  => env('DB_USERNAME_GENMAS', 'rachmad'),
      'password'  => env('DB_PASSWORD_GENMAS', '4esA8udjJ987dVrp'),
      'charset'   => 'utf8',
      'collation' => 'utf8_unicode_ci',
      'prefix'    => '',
      //'strict'    => true,
      'engine'    => null,
    ],

    'db_sidia' => [
      'driver' => 'pgsql',
      'port' => env('DB_PORT_SIDIA', '8483'),
      'host' => env('DB_HOST_SIDIA', '10.10.20.100'),
      'database' => env('DB_DATABASE_SIDIA', 'sidia'),
      'username' => env('DB_USERNAME_SIDIA', 'simpaud'),
      'password' => env('DB_PASSWORD_SIDIA', 'L@mpuNg'),
      'charset' => 'utf8',
      'prefix' => '',
      'schema' => 'public',
      'sslmode' => 'prefer',
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
    | provides a richer body of commands than a typical key-value system
    | such as APC or Memcached. Laravel makes it easy to dig right in.
    |
    */

  'redis' => [

    'client' => env('REDIS_CLIENT', 'phpredis'),

    'options' => [
      'cluster' => env('REDIS_CLUSTER', 'redis'),
      'prefix' => env('REDIS_PREFIX', Str::slug(env('APP_NAME', 'laravel'), '_') . '_database_'),
    ],

    'default' => [
      'url' => env('REDIS_URL'),
      'host' => env('REDIS_HOST', '127.0.0.1'),
      'password' => env('REDIS_PASSWORD', null),
      'port' => env('REDIS_PORT', '6379'),
      'database' => env('REDIS_DB', '0'),
    ],

    'cache' => [
      'url' => env('REDIS_URL'),
      'host' => env('REDIS_HOST', '127.0.0.1'),
      'password' => env('REDIS_PASSWORD', null),
      'port' => env('REDIS_PORT', '6379'),
      'database' => env('REDIS_CACHE_DB', '1'),
    ],

  ],

];
