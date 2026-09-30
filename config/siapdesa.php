<?php

return [
    'version' => env('APP_VERSION', '1.0.0'),
    'schema_version' => env('DB_SCHEMA_VERSION', '1.0.0'),
    'device_uuid' => env('SIAP_DEVICE_UUID'),
    'village_uuid' => env('SIAP_VILLAGE_UUID'),
    'sync' => [
        'enabled' => (bool) env('SYNC_ENABLED', true),
        'batch_size' => (int) env('SYNC_BATCH_SIZE', 100),
        'interval_seconds' => (int) env('SYNC_INTERVAL_SECONDS', 60),
    ],
];
