<?php

namespace App\Http\Controllers;

use App\Models\Village;
use App\Services\Audit\AuditService;
use Illuminate\Http\Request;

class VillageController extends Controller
{
    public function index()
    {
        $village = Village::firstOrFail();
        return view('villages.profile', compact('village'));
    }

    public function update(Request $request)
    {
        $village = Village::firstOrFail();

        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'code' => 'nullable|string|max:30',
            'district' => 'nullable|string|max:100',
            'regency' => 'nullable|string|max:100',
            'province' => 'nullable|string|max:100',
            'address' => 'nullable|string',
            'postal_code' => 'nullable|string|max:10',
            'phone' => 'nullable|string|max:25',
            'email' => 'nullable|email|max:100',
            'website' => 'nullable|string|max:150',
            'head_name' => 'nullable|string|max:150',
            'secretary_name' => 'nullable|string|max:150',
            'letter_number_format' => 'required|string|max:100',
        ]);

        $oldData = $village->toArray();
        $village->update($validated);

        AuditService::log('UPDATE', 'villages', $village->uuid, $oldData, $village->toArray());

        return back()->with('success', 'Profil dan pengaturan desa berhasil diperbarui.');
    }
}
