<?php

return [
    'version' => env('APP_VERSION', '1.0.0'),
    'schema_version' => (int) env('DB_SCHEMA_VERSION', 1),
    'sync_protocol_version' => (int) env('SYNC_PROTOCOL_VERSION', 1),
    'device_uuid' => env('SIAP_DEVICE_UUID'),
    'village_uuid' => env('SIAP_VILLAGE_UUID'),
    'data_path' => env('SIAP_DATA_PATH', storage_path('app')),
    'sync' => [
        'enabled' => (bool) env('SYNC_ENABLED', true),
        'batch_size' => (int) env('SYNC_BATCH_SIZE', 100),
        'interval_seconds' => (int) env('SYNC_INTERVAL_SECONDS', 60),
    ],
];
