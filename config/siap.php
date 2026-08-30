<?php

return [
    'app_version' => '1.0.0',
    'schema_version' => 1,
    'sync_protocol_version' => 1,
    'data_path' => env('SIAP_DATA_PATH', 'C:/ProgramData/SIAP Desa'),
    'central_api_url' => env('CENTRAL_API_URL', 'https://api.siapdesa.id/api/v1'),
    'central_api_key' => env('CENTRAL_API_KEY', 'sb_publishable_UeCe6pqUTtRhBgLjpT1djA_anoXjXve'),
    'supabase_key' => env('SUPABASE_KEY', 'sb_publishable_UeCe6pqUTtRhBgLjpT1djA_anoXjXve'),
    'supabase_url' => env('SUPABASE_URL', 'https://siapdesa.supabase.co'),
    'github_repo' => env('GITHUB_REPO', 'NH-Production/Siap-Desa'),
    'github_url' => env('GITHUB_URL', 'https://github.com/NH-Production/Siap-Desa'),
    'sync_enabled' => env('SYNC_ENABLED', true),
    'auto_backup' => env('AUTO_BACKUP', true),
    'backup_retention_days' => 30,
];
