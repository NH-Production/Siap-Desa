<?php

return [
    'version' => env('APP_VERSION', '1.0.0'),
    'schema_version' => (int) env('DB_SCHEMA_VERSION', 1),
    'sync_protocol_version' => (int) env('SYNC_PROTOCOL_VERSION', 1),
    'device_uuid' => env('SIAP_DEVICE_UUID'),
    'village_uuid' => env('SIAP_VILLAGE_UUID'),
    'data_path' => env('SIAP_DATA_PATH', storage_path('app')),
    'sync' => [
        'enabled' => filter_var(env('SYNC_ENABLED', true), FILTER_VALIDATE_BOOLEAN),
        'batch_size' => (int) env('SYNC_BATCH_SIZE', 100),
        'interval_seconds' => (int) env('SYNC_INTERVAL_SECONDS', 60),
        'timeout_seconds' => (int) env('SYNC_TIMEOUT_SECONDS', 20),
        'max_attempts' => (int) env('SYNC_MAX_ATTEMPTS', 8),
        'central_api_url' => env('CENTRAL_API_URL', ''),
    ],
    'sync_models' => [
        'citizens' => \App\Models\Citizen::class,
        'families' => \App\Models\Family::class,
        'employees' => \App\Models\Employee::class,
        'attendance' => \App\Models\Attendance::class,
        'letters' => \App\Models\Letter::class,
        'assets' => \App\Models\Asset::class,
        'asset_categories' => \App\Models\AssetCategory::class,
        'asset_mutations' => \App\Models\AssetMutation::class,
        'aid_programs' => \App\Models\AidProgram::class,
        'aid_recipients' => \App\Models\AidRecipient::class,
    ],
];
