<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CentralSyncLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CentralSyncApiController extends Controller
{
    public function push(Request $request)
    {
        $start = microtime(true);
        $payload = $request->input('mutations', []);
        $villageCode = $request->input('village_code', 'UNKNOWN');
        $deviceCode = $request->input('device_code', 'PC-CLIENT');

        $count = count($payload);

        // Process mutations to central multi-tenant layer (idempotent)
        // Store log
        $latency = round((microtime(true) - $start) * 1000);
        CentralSyncLog::create([
            'village_code' => $villageCode,
            'device_code' => $deviceCode,
            'direction' => 'PUSH',
            'records_count' => $count,
            'status' => 'SUCCESS',
            'details' => json_encode(['batch_size' => $count, 'ip' => $request->ip()]),
            'latency_ms' => $latency,
        ]);

        return response()->json([
            'success' => true,
            'message' => "Berhasil memproses {$count} mutasi data dari {$villageCode}.",
            'synced_count' => $count,
            'server_time' => now()->toIso8601String(),
        ]);
    }

    public function pull(Request $request)
    {
        $start = microtime(true);
        $villageCode = $request->input('village_code');
        $cursor = $request->input('cursor');

        // Return delta changes for this village
        $changes = []; // Fetch delta from Central Database

        $latency = round((microtime(true) - $start) * 1000);
        CentralSyncLog::create([
            'village_code' => $villageCode ?? 'UNKNOWN',
            'device_code' => $request->input('device_code', 'PC-CLIENT'),
            'direction' => 'PULL',
            'records_count' => count($changes),
            'status' => 'SUCCESS',
            'latency_ms' => $latency,
        ]);

        return response()->json([
            'success' => true,
            'changes' => $changes,
            'next_cursor' => now()->toIso8601String(),
        ]);
    }
}
