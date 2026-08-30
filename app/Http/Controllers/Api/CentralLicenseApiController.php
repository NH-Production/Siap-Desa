<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CentralDevice;
use App\Models\CentralLicense;
use App\Models\CentralServerSetting;
use App\Models\CentralVillage;
use Illuminate\Http\Request;

class CentralLicenseApiController extends Controller
{
    public function config(Request $request)
    {
        $request->validate([
            'license_key' => 'required|string',
            'device_code' => 'required|string',
            'device_name' => 'nullable|string',
            'fingerprint' => 'nullable|string',
            'app_version' => 'nullable|string',
        ]);

        $license = CentralLicense::with('village')->where('license_key', $request->license_key)->first();

        if (!$license) {
            return response()->json([
                'success' => false,
                'message' => 'Kode lisensi tidak ditemukan pada Server SIAP CLOUD.',
            ], 404);
        }

        if ($license->status !== 'ACTIVE') {
            return response()->json([
                'success' => false,
                'message' => "Lisensi desa ini berstatus: {$license->status}. Silakan hubungi Administrator SIAP CLOUD.",
            ], 403);
        }

        if ($license->expiry_date && $license->expiry_date->isPast()) {
            return response()->json([
                'success' => false,
                'message' => 'Masa aktif lisensi telah berakhir pada ' . $license->expiry_date->format('d/m/Y'),
            ], 403);
        }

        $village = $license->village;

        // Ensure village has Client ID
        if (empty($village->client_id)) {
            $cleanName = strtoupper(preg_replace('/[^A-Z0-9]/', '', $village->name));
            $village->client_id = 'CLNT-' . $village->code . '-' . $cleanName;
            $village->save();
        }

        // Lock Client ID to License
        if (empty($license->locked_client_id)) {
            $license->locked_client_id = $village->client_id;
            $license->save();
        }

        // Register / Update Device
        $device = CentralDevice::updateOrCreate(
            ['central_license_id' => $license->id, 'device_code' => $request->device_code],
            [
                'device_name' => $request->device_name ?? 'Local Station',
                'fingerprint' => $request->fingerprint,
                'ip_address' => $request->ip(),
                'app_version' => $request->app_version ?? '1.0.0',
                'last_seen_at' => now(),
                'status' => 'ACTIVE',
            ]
        );

        $supabaseUrl = CentralServerSetting::get('supabase_url', 'https://siapdesa.supabase.co');
        $supabaseKey = CentralServerSetting::get('supabase_key', 'sb_publishable_UeCe6pqUTtRhBgLjpT1djA_anoXjXve');
        $syncInterval = (int)CentralServerSetting::get('sync_interval_seconds', 30);
        $realtimeSync = CentralServerSetting::get('realtime_sync_enabled', '1') === '1';

        return response()->json([
            'success' => true,
            'message' => 'Konfigurasi server berhasil ditarik & Client ID terkunci.',
            'data' => [
                'client_id' => $village->client_id,
                'license_key' => $license->license_key,
                'tier' => $license->tier,
                'village_code' => $village->code,
                'village_name' => $village->name,
                'district' => $village->district,
                'regency' => $village->regency,
                'province' => $village->province,
                'max_devices' => $license->max_devices,
                'supabase_url' => $supabaseUrl,
                'supabase_key' => $supabaseKey,
                'sync_interval_seconds' => $syncInterval,
                'realtime_sync_enabled' => $realtimeSync,
                'sync_token' => hash('sha256', $license->license_key . '|' . $village->client_id . '|' . $device->device_code),
            ]
        ]);
    }

    public function verify(Request $request)
    {
        return $this->config($request);
    }
}
