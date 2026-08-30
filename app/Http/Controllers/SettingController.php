<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\SystemSetting;
use App\Models\SystemVersion;
use App\Models\Village;
use App\Services\Audit\AuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;

class SettingController extends Controller
{
    public function index()
    {
        $version = SystemVersion::orderBy('id', 'desc')->first();
        $device = Device::first();
        $village = Village::first();

        $settings = [
            'central_api_url' => config('siap.central_api_url', 'https://api.siapdesa.id/api/v1'),
            'central_api_key' => config('siap.central_api_key', ''),
            'sync_enabled' => config('siap.sync_enabled', true),
            'auto_backup' => config('siap.auto_backup', true),
            'db_connection' => config('database.default', 'sqlite'),
            'db_host' => config('database.connections.mariadb.host', '127.0.0.1'),
            'db_port' => config('database.connections.mariadb.port', '3306'),
            'db_database' => config('database.connections.mariadb.database', 'siap_desa'),
            'db_username' => config('database.connections.mariadb.username', 'root'),
        ];

        return view('system.settings', compact('version', 'device', 'village', 'settings'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'central_api_url' => 'required|url',
            'central_api_key' => 'nullable|string',
            'sync_enabled' => 'nullable|boolean',
            'auto_backup' => 'nullable|boolean',
        ]);

        $envFile = base_path('.env');
        if (File::exists($envFile)) {
            $env = File::get($envFile);
            $env = preg_replace('/CENTRAL_API_URL=.*/', 'CENTRAL_API_URL="' . $validated['central_api_url'] . '"', $env);
            $env = preg_replace('/CENTRAL_API_KEY=.*/', 'CENTRAL_API_KEY="' . ($validated['central_api_key'] ?? '') . '"', $env);
            $env = preg_replace('/SYNC_ENABLED=.*/', 'SYNC_ENABLED=' . ($request->boolean('sync_enabled') ? 'true' : 'false'), $env);
            $env = preg_replace('/AUTO_BACKUP=.*/', 'AUTO_BACKUP=' . ($request->boolean('auto_backup') ? 'true' : 'false'), $env);
            File::put($envFile, $env);
        }

        AuditService::log('UPDATE', 'system_settings', null, null, $validated);

        return back()->with('success', 'Konfigurasi server central & sinkronisasi berhasil disimpan.');
    }

    public function testCentralConnection(Request $request)
    {
        $url = rtrim($request->input('url', config('siap.central_api_url')), '/');
        $apiKey = $request->input('key', config('siap.central_api_key'));

        try {
            $start = microtime(true);
            $response = Http::timeout(5)
                ->withHeaders(['Authorization' => 'Bearer ' . $apiKey])
                ->get($url . '/status');

            $latency = round((microtime(true) - $start) * 1000);

            if ($response->successful()) {
                return response()->json([
                    'success' => true,
                    'message' => "Koneksi ke Central Cloud Server Berhasil! (Latency: {$latency}ms)",
                    'data' => $response->json(),
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => "Server merespons dengan HTTP Status {$response->status()}",
                ], 400);
            }
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => "Gagal terhubung ke Central Server: " . $e->getMessage(),
            ], 500);
        }
    }
}
