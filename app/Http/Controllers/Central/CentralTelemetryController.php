<?php

namespace App\Http\Controllers\Central;

use App\Http\Controllers\Controller;
use App\Models\CentralDevice;
use App\Models\CentralLicense;
use App\Models\CentralSyncLog;
use App\Models\CentralVillage;
use Illuminate\Http\Request;

class CentralTelemetryController extends Controller
{
    public function index(Request $request)
    {
        $query = CentralSyncLog::latest();
        if ($request->filled('direction')) {
            $query->where('direction', $request->direction);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        $logs = $query->paginate(25);
        return view('central.telemetry', compact('logs'));
    }

    public function liveData()
    {
        $todaySyncs = CentralSyncLog::whereDate('created_at', today())->count();
        $activeDevices = CentralDevice::where('status', 'ACTIVE')->where('last_seen_at', '>=', now()->subMinutes(15))->count();
        $totalDevices = CentralDevice::count();
        $recentLogs = CentralSyncLog::latest()->take(8)->get()->map(function ($log) {
            return [
                'time' => $log->created_at->format('H:i:s'),
                'village_code' => $log->village_code,
                'device_code' => $log->device_code,
                'direction' => $log->direction,
                'records_count' => $log->records_count,
                'latency_ms' => $log->latency_ms,
                'status' => $log->status,
            ];
        });

        return response()->json([
            'today_syncs' => $todaySyncs,
            'active_devices' => $activeDevices,
            'total_devices' => $totalDevices,
            'recent_logs' => $recentLogs,
            'timestamp' => now()->toIso8601String(),
        ]);
    }
}
