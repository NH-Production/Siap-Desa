<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CentralDevice;
use App\Models\CentralLicense;
use App\Models\CentralVillage;
use Illuminate\Http\Request;

class CentralLicenseApiController extends Controller
{
    public function verify(Request $request)
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
                'message' => 'Kode lisensi tidak valid atau tidak terdaftar di Central Server.',
            ], 404);
        }

        if ($license->status !== 'ACTIVE') {
            return response()->json([
                'success' => false,
                'message' => "Lisensi ini berstatus: {$license->status}. Silakan hubungi Administrator Cloud.",
            ], 403);
        }

        if ($license->expiry_date && $license->expiry_date->isPast()) {
            return response()->json([
                'success' => false,
                'message' => 'Masa aktif lisensi telah berakhir pada ' . $license->expiry_date->format('d/m/Y'),
            ], 403);
        }

        // Register or update device
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

        $activeDevicesCount = CentralDevice::where('central_license_id', $license->id)->where('status', 'ACTIVE')->count();

        return response()->json([
            'success' => true,
            'message' => 'Lisensi terverifikasi aktif (Enterprise SAAS).',
            'data' => [
                'village_code' => $license->village->code,
                'village_name' => $license->village->name,
                'district' => $license->village->district,
                'regency' => $license->village->regency,
                'province' => $license->village->province,
                'tier' => $license->tier,
                'max_devices' => $license->max_devices,
                'active_devices' => $activeDevicesCount,
                'expiry_date' => $license->expiry_date ? $license->expiry_date->format('Y-m-d') : null,
                'sync_token' => hash('sha256', $license->license_key . '|' . $device->device_code),
            ]
        ]);
    }
}
