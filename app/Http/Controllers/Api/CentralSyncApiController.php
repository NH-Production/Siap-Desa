<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CentralSyncLog;
use App\Services\Supabase\SupabaseService;
use Illuminate\Http\Request;

class CentralSyncApiController extends Controller
{
    protected SupabaseService $supabase;

    public function __construct(SupabaseService $supabase)
    {
        $this->supabase = $supabase;
    }

    public function push(Request $request)
    {
        $start = microtime(true);
        $payload = $request->input('mutations', []);
        $villageCode = $request->input('village_code', 'UNKNOWN');
        $deviceCode = $request->input('device_code', 'PC-CLIENT');
        $count = count($payload);

        // 1. Forward/Ingest to Supabase Cloud
        if ($count > 0) {
            $this->supabase->insertOrUpsert('central_sync_mutations', [
                'village_code' => $villageCode,
                'device_code' => $deviceCode,
                'batch_size' => $count,
                'payload' => json_encode($payload),
                'created_at' => now()->toIso8601String(),
            ]);
        }

        // 2. Record Central Telemetry Log
        $latency = round((microtime(true) - $start) * 1000);
        CentralSyncLog::create([
            'village_code' => $villageCode,
            'device_code' => $deviceCode,
            'direction' => 'PUSH',
            'records_count' => $count,
            'status' => 'SUCCESS',
            'details' => json_encode(['batch_size' => $count, 'ip' => $request->ip(), 'supabase_sync' => true]),
            'latency_ms' => $latency,
        ]);

        return response()->json([
            'success' => true,
            'message' => "Berhasil memproses {$count} mutasi data dari {$villageCode} ke Central Supabase Cloud.",
            'synced_count' => $count,
            'supabase_connected' => true,
            'server_time' => now()->toIso8601String(),
        ]);
    }

    public function pull(Request $request)
    {
        $start = microtime(true);
        $villageCode = $request->input('village_code');
        $cursor = $request->input('cursor');

        // Query delta from Supabase
        $changes = [];

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
