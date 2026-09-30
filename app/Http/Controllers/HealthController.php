<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class HealthController extends Controller
{
    public function __invoke()
    {
        try { DB::connection()->getPdo(); $local = 'ok'; }
        catch (\Throwable $e) { $local = 'error'; }

        $central = 'disabled';
        if (env('CENTRAL_DB_HOST')) {
            try { DB::connection('central_mysql')->getPdo(); $central = 'ok'; }
            catch (\Throwable $e) { $central = 'offline'; }
        }

        return response()->json([
            'application' => 'SIAP Desa',
            'status' => $local === 'ok' ? 'ok' : 'degraded',
            'mode' => $central === 'ok' ? 'online' : 'offline-capable',
            'local_database' => $local,
            'central_database' => $central,
            'sync_enabled' => (bool) env('SYNC_ENABLED', true),
            'device_uuid' => env('SIAP_DEVICE_UUID'),
            'timestamp' => now()->toIso8601String(),
        ], $local === 'ok' ? 200 : 503);
    }
}
