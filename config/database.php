<?php

$dataPath = env('SIAP_DATA_PATH', 'C:\\ProgramData\\SIAP Desa');

return [
    'default' => env('DB_CONNECTION', 'mysql'),
    'connections' => [
        'mysql' => [
            'driver' => 'mysql',
            'url' => env('DB_URL'),
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '3306'),
            'database' => env('DB_DATABASE', 'siap_desa_local'),
            'username' => env('DB_USERNAME', 'root'),
            'password' => env('DB_PASSWORD', ''),
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => true,
            'engine' => null,
        ],
        'central_mysql' => [
            'driver' => 'mysql',
            'url' => env('CENTRAL_DB_URL'),
            'host' => env('CENTRAL_DB_HOST'),
            'port' => env('CENTRAL_DB_PORT', '3306'),
            'database' => env('CENTRAL_DB_DATABASE', 'siap_desa_cloud'),
            'username' => env('CENTRAL_DB_USERNAME'),
            'password' => env('CENTRAL_DB_PASSWORD'),
            'unix_socket' => env('CENTRAL_DB_SOCKET'),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => true,
            'engine' => null,
        ],
    ],
    'migrations' => ['table' => 'migrations', 'update_date_on_publish' => true],
];
