<?php

namespace App\Http\Controllers;

use App\Models\Citizen;
use App\Models\Village;
use App\Services\Audit\AuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CitizenController extends Controller
{
    public function index(Request $request)
    {
        $query = Citizen::query();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('nik', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%")
                  ->orWhere('no_kk', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%");
            });
        }

        if ($gender = $request->input('gender')) {
            $query->where('gender', $gender);
        }

        if ($religion = $request->input('religion')) {
            $query->where('religion', $religion);
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $citizens = $query->latest()->paginate(15)->withQueryString();

        return view('citizens.index', compact('citizens'));
    }

    public function create()
    {
        return view('citizens.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nik' => 'required|digits:16|unique:citizens,nik',
            'no_kk' => 'nullable|digits:16',
            'name' => 'required|string|max:150',
            'birth_place' => 'nullable|string|max:80',
            'birth_date' => 'nullable|date',
            'gender' => 'required|in:LAKI_LAKI,PEREMPUAN',
            'blood_type' => 'nullable|string|max:5',
            'religion' => 'nullable|string|max:30',
            'marital_status' => 'nullable|string|max:30',
            'occupation' => 'nullable|string|max:80',
            'education' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'rt' => 'nullable|string|max:5',
            'rw' => 'nullable|string|max:5',
            'status' => 'required|in:TETAP,PINDAH,MENINGGAL,SEMENTARA',
            'notes' => 'nullable|string',
        ]);

        $village = Village::first();
        $validated['village_id'] = $village->uuid ?? Str::uuid()->toString();

        $citizen = Citizen::create($validated);

        AuditService::log('CREATE', 'citizens', $citizen->uuid, null, $citizen->toArray());

        return redirect()->route('citizens.index')->with('success', 'Data penduduk berhasil ditambahkan.');
    }

    public function show($uuid)
    {
        $citizen = Citizen::where('uuid', $uuid)->with('familyMember.family', 'employee')->firstOrFail();
        return view('citizens.show', compact('citizen'));
    }

    public function edit($uuid)
    {
        $citizen = Citizen::where('uuid', $uuid)->firstOrFail();
        return view('citizens.edit', compact('citizen'));
    }

    public function update(Request $request, $uuid)
    {
        $citizen = Citizen::where('uuid', $uuid)->firstOrFail();

        $validated = $request->validate([
            'nik' => 'required|digits:16|unique:citizens,nik,' . $citizen->id,
            'no_kk' => 'nullable|digits:16',
            'name' => 'required|string|max:150',
            'birth_place' => 'nullable|string|max:80',
            'birth_date' => 'nullable|date',
            'gender' => 'required|in:LAKI_LAKI,PEREMPUAN',
            'blood_type' => 'nullable|string|max:5',
            'religion' => 'nullable|string|max:30',
            'marital_status' => 'nullable|string|max:30',
            'occupation' => 'nullable|string|max:80',
            'education' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'rt' => 'nullable|string|max:5',
            'rw' => 'nullable|string|max:5',
            'status' => 'required|in:TETAP,PINDAH,MENINGGAL,SEMENTARA',
            'notes' => 'nullable|string',
        ]);

        $oldData = $citizen->toArray();
        $citizen->update($validated);

        AuditService::log('UPDATE', 'citizens', $citizen->uuid, $oldData, $citizen->toArray());

        return redirect()->route('citizens.show', $citizen->uuid)->with('success', 'Data penduduk berhasil diperbarui.');
    }

    public function destroy($uuid)
    {
        $citizen = Citizen::where('uuid', $uuid)->firstOrFail();
        $oldData = $citizen->toArray();
        $citizen->delete();

        AuditService::log('DELETE', 'citizens', $citizen->uuid, $oldData);

        return redirect()->route('citizens.index')->with('success', 'Data penduduk telah dihapus (soft-delete).');
    }

    public function exportCsv()
    {
        $citizens = Citizen::all();
        $filename = 'penduduk_desa_' . date('Ymd_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($citizens) {
            $file = fopen('php://output', 'w');
            // Add UTF-8 BOM
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($file, ['NIK', 'No KK', 'Nama Lengkap', 'Jenis Kelamin', 'Tempat Lahir', 'Tanggal Lahir', 'Agama', 'Status Kawin', 'Pekerjaan', 'Pendidikan', 'Alamat', 'RT', 'RW', 'Status']);

            foreach ($citizens as $c) {
                fputcsv($file, [
                    $c->nik,
                    $c->no_kk,
                    $c->name,
                    $c->gender,
                    $c->birth_place,
                    $c->birth_date ? $c->birth_date->format('Y-m-d') : '',
                    $c->religion,
                    $c->marital_status,
                    $c->occupation,
                    $c->education,
                    $c->address,
                    $c->rt,
                    $c->rw,
                    $c->status,
                ]);
            }
            fclose($file);
        };

        AuditService::log('EXPORT', 'citizens', null, null, ['format' => 'CSV']);

        return response()->stream($callback, 200, $headers);
    }
}
