<?php

namespace App\Http\Controllers\Central;

use App\Http\Controllers\Controller;
use App\Models\CentralLicense;
use App\Models\CentralVillage;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CentralVillageController extends Controller
{
    public function index(Request $request)
    {
        $query = CentralVillage::with(['licenses', 'activeLicense']);
        if ($request->filled('q')) {
            $q = $request->q;
            $query->where('name', 'like', "%{$q}%")
                  ->orWhere('code', 'like', "%{$q}%")
                  ->orWhere('regency', 'like', "%{$q}%");
        }
        $villages = $query->paginate(15);
        return view('central.villages.index', compact('villages'));
    }

    public function create()
    {
        return view('central.villages.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|unique:central_villages,code',
            'name' => 'required|string|max:100',
            'district' => 'required|string|max:100',
            'regency' => 'required|string|max:100',
            'province' => 'required|string|max:100',
            'head_name' => 'nullable|string|max:100',
            'phone' => 'nullable|string|max:30',
            'email' => 'nullable|email|max:100',
            'status' => 'required|string|in:ACTIVE,TRIAL,SUSPENDED',
            'address' => 'nullable|string',
        ]);

        $village = CentralVillage::create($validated);

        // Auto-generate initial license
        $licenseKey = 'SIAP-SAAS-' . substr($village->code, 0, 4) . '-ENT-' . strtoupper(Str::random(4)) . '-' . date('Y');
        CentralLicense::create([
            'central_village_id' => $village->id,
            'license_key' => $licenseKey,
            'tier' => 'ENTERPRISE',
            'max_devices' => 10,
            'issued_date' => now(),
            'expiry_date' => now()->addYear(),
            'status' => 'ACTIVE',
            'notes' => 'Lisensi awal aktivasi desa',
        ]);

        return redirect()->route('central.villages.index')->with('success', "Desa {$village->name} berhasil didaftarkan dengan Lisensi: {$licenseKey}");
    }
}
