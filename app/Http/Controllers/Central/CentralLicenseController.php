<?php

namespace App\Http\Controllers\Central;

use App\Http\Controllers\Controller;
use App\Models\CentralLicense;
use App\Models\CentralVillage;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CentralLicenseController extends Controller
{
    public function index(Request $request)
    {
        $query = CentralLicense::with(['village', 'devices']);
        if ($request->filled('tier')) {
            $query->where('tier', $request->tier);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        $licenses = $query->latest()->paginate(15);
        $villages = CentralVillage::orderBy('name')->get();

        return view('central.licenses.index', compact('licenses', 'villages'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'central_village_id' => 'required|exists:central_villages,id',
            'tier' => 'required|string|in:TRIAL,STANDARD,ENTERPRISE',
            'max_devices' => 'required|integer|min:1|max:50',
            'duration_months' => 'required|integer|min:1|max:60',
        ]);

        $village = CentralVillage::findOrFail($request->central_village_id);
        $tierCode = substr($request->tier, 0, 3);
        $licenseKey = 'SIAP-SAAS-' . substr($village->code, 0, 4) . "-{$tierCode}-" . strtoupper(Str::random(4)) . '-' . date('Y');

        CentralLicense::create([
            'central_village_id' => $village->id,
            'license_key' => $licenseKey,
            'tier' => $request->tier,
            'max_devices' => $request->max_devices,
            'issued_date' => now(),
            'expiry_date' => now()->addMonths($request->duration_months),
            'status' => 'ACTIVE',
            'notes' => $request->notes,
        ]);

        return back()->with('success', "Kode Lisensi baru berhasil digenerate: {$licenseKey}");
    }

    public function toggleStatus($uuid)
    {
        $license = CentralLicense::where('uuid', $uuid)->firstOrFail();
        $license->status = $license->status === 'ACTIVE' ? 'REVOKED' : 'ACTIVE';
        $license->save();

        return back()->with('success', "Status lisensi {$license->license_key} diperbarui menjadi {$license->status}.");
    }
}
