<?php

namespace App\Http\Controllers;

use App\Models\Citizen;
use App\Models\Family;
use App\Models\FamilyMember;
use App\Models\Village;
use App\Services\Audit\AuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class FamilyController extends Controller
{
    public function index(Request $request)
    {
        $query = Family::with('members.citizen');

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('no_kk', 'like', "%{$search}%")
                  ->orWhere('head_name', 'like', "%{$search}%")
                  ->orWhere('head_nik', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%");
            });
        }

        $families = $query->latest()->paginate(15)->withQueryString();

        return view('families.index', compact('families'));
    }

    public function create()
    {
        return view('families.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'no_kk' => 'required|digits:16|unique:families,no_kk',
            'head_name' => 'required|string|max:150',
            'head_nik' => 'nullable|digits:16',
            'address' => 'nullable|string',
            'rt' => 'nullable|string|max:5',
            'rw' => 'nullable|string|max:5',
            'postal_code' => 'nullable|string|max:10',
            'issue_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        $village = Village::first();
        $validated['village_id'] = $village->uuid ?? Str::uuid()->toString();

        $family = Family::create($validated);

        // Link head of family if citizen exists
        if (!empty($validated['head_nik'])) {
            $citizen = Citizen::where('nik', $validated['head_nik'])->first();
            if ($citizen) {
                FamilyMember::firstOrCreate([
                    'family_id' => $family->id,
                    'citizen_id' => $citizen->id,
                ], [
                    'relation_status' => 'KEPALA_KELUARGA',
                ]);
            }
        }

        AuditService::log('CREATE', 'families', $family->uuid, null, $family->toArray());

        return redirect()->route('families.show', $family->uuid)->with('success', 'Kartu Keluarga berhasil ditambahkan.');
    }

    public function show($uuid)
    {
        $family = Family::where('uuid', $uuid)->with('members.citizen')->firstOrFail();
        $availableCitizens = Citizen::whereDoesntHave('familyMember')->orWhereHas('familyMember', function ($q) use ($family) {
            $q->where('family_id', $family->id);
        })->get();

        return view('families.show', compact('family', 'availableCitizens'));
    }

    public function edit($uuid)
    {
        $family = Family::where('uuid', $uuid)->firstOrFail();
        return view('families.edit', compact('family'));
    }

    public function update(Request $request, $uuid)
    {
        $family = Family::where('uuid', $uuid)->firstOrFail();

        $validated = $request->validate([
            'no_kk' => 'required|digits:16|unique:families,no_kk,' . $family->id,
            'head_name' => 'required|string|max:150',
            'head_nik' => 'nullable|digits:16',
            'address' => 'nullable|string',
            'rt' => 'nullable|string|max:5',
            'rw' => 'nullable|string|max:5',
            'postal_code' => 'nullable|string|max:10',
            'issue_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        $oldData = $family->toArray();
        $family->update($validated);

        AuditService::log('UPDATE', 'families', $family->uuid, $oldData, $family->toArray());

        return redirect()->route('families.show', $family->uuid)->with('success', 'Data Kartu Keluarga berhasil diperbarui.');
    }

    public function destroy($uuid)
    {
        $family = Family::where('uuid', $uuid)->firstOrFail();
        $oldData = $family->toArray();
        $family->delete();

        AuditService::log('DELETE', 'families', $family->uuid, $oldData);

        return redirect()->route('families.index')->with('success', 'Data Kartu Keluarga telah dihapus.');
    }

    public function addMember(Request $request, $uuid)
    {
        $family = Family::where('uuid', $uuid)->firstOrFail();

        $validated = $request->validate([
            'citizen_id' => 'required|exists:citizens,id',
            'relation_status' => 'required|string|max:30',
        ]);

        FamilyMember::updateOrCreate(
            ['citizen_id' => $validated['citizen_id']],
            [
                'family_id' => $family->id,
                'relation_status' => $validated['relation_status'],
            ]
        );

        $citizen = Citizen::find($validated['citizen_id']);
        if ($citizen) {
            $citizen->update(['no_kk' => $family->no_kk]);
        }

        AuditService::log('UPDATE', 'family_members', $family->uuid, null, $validated);

        return back()->with('success', 'Anggota keluarga berhasil ditambahkan ke Kartu Keluarga.');
    }

    public function removeMember($memberUuid)
    {
        $member = FamilyMember::where('uuid', $memberUuid)->firstOrFail();
        $member->delete();

        return back()->with('success', 'Anggota keluarga berhasil dikeluarkan dari KK.');
    }
}
