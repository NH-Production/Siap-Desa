<?php

namespace App\Http\Controllers;

use App\Models\Citizen;
use App\Models\Employee;
use App\Models\Village;
use App\Services\Audit\AuditService;
use App\Services\QrCode\QrService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class EmployeeController extends Controller
{
    public function index()
    {
        $employees = Employee::with('citizen')->latest()->paginate(15);
        return view('employees.index', compact('employees'));
    }

    public function create()
    {
        $citizens = Citizen::all();
        return view('employees.create', compact('citizens'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'citizen_id' => 'nullable|exists:citizens,id',
            'nip' => 'nullable|string|max:30',
            'position' => 'required|string|max:80',
            'department' => 'nullable|string|max:80',
            'employment_status' => 'required|string|max:30',
            'join_date' => 'nullable|date',
        ]);

        $village = Village::first();
        $validated['village_id'] = $village->uuid ?? Str::uuid()->toString();
        $validated['qr_token'] = 'QR-EMP-' . strtoupper(Str::random(16));
        $validated['is_active'] = true;

        $employee = Employee::create($validated);

        AuditService::log('CREATE', 'employees', $employee->uuid, null, $employee->toArray());

        return redirect()->route('employees.index')->with('success', 'Data pegawai/aparat desa berhasil ditambahkan.');
    }

    public function show($uuid)
    {
        $employee = Employee::where('uuid', $uuid)->with('citizen', 'positions', 'attendances')->firstOrFail();
        return view('employees.show', compact('employee'));
    }

    public function qrCard($uuid)
    {
        $employee = Employee::where('uuid', $uuid)->firstOrFail();
        $village = Village::first();

        $qrService = new QrService();
        $qrDataUri = $qrService->generateDataUri($employee->qr_token, 250);

        return view('employees.qr-card', compact('employee', 'village', 'qrDataUri'));
    }

    public function rotateQr($uuid)
    {
        $employee = Employee::where('uuid', $uuid)->firstOrFail();
        $newToken = 'QR-EMP-' . strtoupper(Str::random(16));
        $employee->update(['qr_token' => $newToken]);

        AuditService::log('UPDATE', 'employees', $employee->uuid, null, ['action' => 'ROTATE_QR']);

        return back()->with('success', 'Token QR Absensi pegawai berhasil dirotasi ulang.');
    }

    public function destroy($uuid)
    {
        $employee = Employee::where('uuid', $uuid)->firstOrFail();
        $employee->delete();

        AuditService::log('DELETE', 'employees', $employee->uuid);

        return redirect()->route('employees.index')->with('success', 'Pegawai telah dinonaktifkan/dihapus.');
    }
}
