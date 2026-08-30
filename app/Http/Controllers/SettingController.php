<?php

namespace App\Http\Controllers;

use App\Models\SystemSetting;
use App\Models\Village;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class SettingController extends Controller
{
    public function index()
    {
        $village = Village::first();
        $settings = [
            'village_name' => SystemSetting::get('village_name', $village->name ?? 'Sindangresmi'),
            'village_code' => SystemSetting::get('village_code', $village->code ?? '3203162002'),
            'locked_client_id' => SystemSetting::get('locked_client_id', 'CLNT-3203162002-SINDANGRESMI'),
            'saas_license_key' => SystemSetting::get('saas_license_key', 'SIAP-SAAS-3203-1620-02-2026'),
            'saas_status' => SystemSetting::get('saas_status', 'ACTIVE_ENTERPRISE'),
            'central_api_url' => SystemSetting::get('central_api_url', 'http://127.0.0.1:8090/api/v1'),
            'sync_interval_seconds' => SystemSetting::get('sync_interval_seconds', '30'),
            'realtime_sync' => SystemSetting::get('realtime_sync_enabled', '1'),
            'device_name' => session('device_name', 'PC-ADMIN-01'),
        ];

        return view('system.settings', compact('settings', 'village'));
    }

    public function activateLicense(Request $request)
    {
        $request->validate([
            'license_key' => 'required|string',
            'central_api_url' => 'required|url',
        ]);

        $centralUrl = rtrim($request->central_api_url, '/');
        $licenseKey = trim($request->license_key);

        try {
            $response = Http::timeout(8)->post($centralUrl . '/client/config', [
                'license_key' => $licenseKey,
                'device_code' => session('device_name', 'PC-ADMIN-01'),
                'device_name' => 'Stasiun Kerja Komputer Desa',
                'app_version' => '1.0.0',
            ]);

            if ($response->successful()) {
                $data = $response->json('data');

                SystemSetting::set('saas_license_key', $licenseKey);
                SystemSetting::set('locked_client_id', $data['client_id']);
                SystemSetting::set('central_api_url', $centralUrl);
                SystemSetting::set('supabase_url', $data['supabase_url'] ?? 'https://siapdesa.supabase.co');
                SystemSetting::set('supabase_key', $data['supabase_key'] ?? '');
                SystemSetting::set('central_api_key', $data['supabase_key'] ?? '');
                SystemSetting::set('sync_interval_seconds', (string)($data['sync_interval_seconds'] ?? 30));
                SystemSetting::set('realtime_sync_enabled', ($data['realtime_sync_enabled'] ?? true) ? '1' : '0');
                SystemSetting::set('saas_status', 'ACTIVE_' . ($data['tier'] ?? 'ENTERPRISE'));

                return response()->json([
                    'success' => true,
                    'message' => "Lisensi Terverifikasi! Client ID terkunci: {$data['client_id']}",
                    'client_id' => $data['client_id'],
                    'village_name' => $data['village_name'],
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => $response->json('message') ?? 'Gagal memverifikasi lisensi ke SIAP CLOUD.',
            ], 400);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak dapat terhubung ke Server SIAP CLOUD: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function testCentralConnection(Request $request)
    {
        return $this->activateLicense($request);
    }
}
