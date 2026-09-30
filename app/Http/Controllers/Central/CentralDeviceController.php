<?php

namespace App\Http\Controllers\Central;

use App\Http\Controllers\Controller;
use App\Models\Device;
use App\Models\Village;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CentralDeviceController extends Controller
{
    public function index(Request $request)
    {
        $query = Device::with('village')->latest('last_seen_at');

        if ($request->filled('village_uuid')) {
            $query->where('village_uuid', $request->string('village_uuid'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        $devices = $query->paginate(30)->withQueryString();
        $villages = Village::orderBy('name')->get(['uuid','name']);

        return view('central.devices.index', compact('devices','villages'));
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'village_uuid' => ['required','uuid','exists:villages,uuid'],
            'device_name' => ['required','string','max:150'],
            'app_version' => ['nullable','string','max:30'],
            'schema_version' => ['nullable','string','max:30'],
        ]);

        $device = Device::create([
            'uuid' => (string) Str::uuid(),
            'village_uuid' => $data['village_uuid'],
            'device_name' => $data['device_name'],
            'app_version' => $data['app_version'] ?? null,
            'schema_version' => $data['schema_version'] ?? null,
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return back()->with('success', 'Device berhasil didaftarkan: '.$device->uuid);
    }

    public function toggle(Device $device)
    {
        $device->status = $device->status === 'active' ? 'blocked' : 'active';
        $device->updated_at = now();
        $device->save();

        return back()->with('success', 'Status device diperbarui.');
    }

    public function retire(Device $device)
    {
        $device->status = 'retired';
        $device->revoked_at = now();
        $device->updated_at = now();
        $device->save();

        return back()->with('success', 'Device dipensiunkan.');
    }
}
