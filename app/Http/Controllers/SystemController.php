<?php

namespace App\Http\Controllers;

use App\Services\AppVersionService;
use App\Services\DeviceIdentityService;

class SystemController extends Controller
{
    public function version(AppVersionService $versions, DeviceIdentityService $deviceIdentity)
    {
        $current = $versions->current();
        $device = $deviceIdentity->uuid();

        return response()->json([
            'product' => config('app.name', 'SIAP Desa'),
            'version' => $current['app_version'],
            'schema_version' => $current['schema_version'],
            'sync_protocol_version' => $current['sync_protocol_version'],
            'device_uuid' => $device,
            'environment' => app()->environment(),
            'timestamp' => now()->toIso8601String(),
        ]);
    }
}
