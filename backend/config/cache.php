<?php

return [
    'default' => env('CACHE_STORE', 'database'),

    'stores' => [
        'array' => [
            'driver' => 'array',
            'serialize' => false,
        ],

        'database' => [
            'driver' => 'database',
            'connection' => env('DB_CONNECTION', 'mysql'),
            'table' => env('CACHE_TABLE', 'cache'),
            'lock_connection' => env('CACHE_LOCK_CONNECTION'),
            'lock_table' => env('CACHE_LOCK_TABLE'),
        ],

        'file' => [
            'driver' => 'file',
            'path'   => storage_path('framework/cache/data'),
            'lock_path' => storage_path('framework/cache/data'),
        ],

        'redis' => [
            'driver' => 'redis',
            'connection' => env('CACHE_REDIS_CONNECTION', 'cache'),
            'lock_connection' => env('CACHE_LOCK_REDIS_CONNECTION', 'default'),
        ],
    ],

    'prefix' => env('CACHE_PREFIX', 'rodud_cache_'),
];
