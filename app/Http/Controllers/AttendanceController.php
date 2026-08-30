<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\AttendanceCorrection;
use App\Models\Device;
use App\Models\Employee;
use App\Models\Village;
use App\Services\Audit\AuditService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $date = $request->input('date', Carbon::today()->toDateString());
        $attendances = Attendance::where('date', $date)->with('employee')->latest()->get();
        $employees = Employee::where('is_active', true)->get();

        return view('attendance.index', compact('attendances', 'employees', 'date'));
    }

    public function scanner()
    {
        return view('attendance.scanner');
    }

    public function scanSubmit(Request $request)
    {
        $request->validate([
            'qr_token' => 'required|string',
        ]);

        $token = trim($request->input('qr_token'));
        $employee = Employee::where('qr_token', $token)->where('is_active', true)->first();

        if (!$employee) {
            return response()->json([
                'success' => false,
                'message' => 'Kartu QR tidak dikenali atau status pegawai tidak aktif.',
            ], 404);
        }

        $today = Carbon::today()->toDateString();
        $nowTime = Carbon::now()->format('H:i:s');
        $device = Device::first();
        $village = Village::first();

        $existing = Attendance::where('employee_id', $employee->id)->where('date', $today)->first();

        if (!$existing) {
            // Check in
            $status = (Carbon::now()->format('H:i:s') > '08:00:00') ? 'TERLAMBAT' : 'HADIR';
            $att = Attendance::create([
                'village_id' => $village->uuid ?? null,
                'employee_id' => $employee->id,
                'device_id' => $device->uuid ?? null,
                'date' => $today,
                'time_in' => $nowTime,
                'status' => $status,
                'notes' => 'Absensi masuk via scan QR',
            ]);

            AuditService::log('CREATE', 'attendance', $att->uuid, null, ['employee' => $employee->name, 'type' => 'CHECK_IN']);

            return response()->json([
                'success' => true,
                'type' => 'IN',
                'employee_name' => $employee->name,
                'position' => $employee->position,
                'time' => $nowTime,
                'status' => $status,
                'message' => "Absensi Masuk Tercatat: {$employee->name} ({$status})",
            ]);
        } else {
            // Check out
            $existing->update([
                'time_out' => $nowTime,
            ]);

            AuditService::log('UPDATE', 'attendance', $existing->uuid, null, ['employee' => $employee->name, 'type' => 'CHECK_OUT']);

            return response()->json([
                'success' => true,
                'type' => 'OUT',
                'employee_name' => $employee->name,
                'position' => $employee->position,
                'time' => $nowTime,
                'status' => $existing->status,
                'message' => "Absensi Pulang Tercatat: {$employee->name} pukul {$nowTime}",
            ]);
        }
    }

    public function manualStore(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'date' => 'required|date',
            'time_in' => 'nullable',
            'time_out' => 'nullable',
            'status' => 'required|in:HADIR,TERLAMBAT,PULANG_CEPAT,IZIN,SAKIT,DINAS_LUAR,ALPHA',
            'notes' => 'nullable|string',
        ]);

        $village = Village::first();
        $device = Device::first();
        $validated['village_id'] = $village->uuid ?? null;
        $validated['device_id'] = $device->uuid ?? null;
        $validated['verified_by'] = Auth::id();

        Attendance::updateOrCreate(
            [
                'employee_id' => $validated['employee_id'],
                'date' => $validated['date'],
            ],
            $validated
        );

        return back()->with('success', 'Data absensi manual berhasil disimpan.');
    }

    public function requestCorrection(Request $request)
    {
        $validated = $request->validate([
            'attendance_id' => 'required|exists:attendance,id',
            'requested_status' => 'required|string',
            'requested_time_in' => 'nullable',
            'requested_time_out' => 'nullable',
            'reason' => 'required|string',
        ]);

        $att = Attendance::findOrFail($validated['attendance_id']);

        AttendanceCorrection::create([
            'attendance_id' => $att->id,
            'employee_id' => $att->employee_id,
            'requested_status' => $validated['requested_status'],
            'requested_time_in' => $validated['requested_time_in'],
            'requested_time_out' => $validated['requested_time_out'],
            'reason' => $validated['reason'],
            'status' => 'PENDING',
        ]);

        return back()->with('success', 'Pengajuan koreksi absensi telah dikirim untuk disetujui.');
    }

    public function resolveCorrection(Request $request, $uuid)
    {
        $correction = AttendanceCorrection::where('uuid', $uuid)->firstOrFail();
        $action = $request->input('action'); // APPROVE or REJECT

        if ($action === 'APPROVE') {
            $correction->update([
                'status' => 'APPROVED',
                'approved_by' => Auth::id(),
                'approved_at' => now(),
            ]);

            $correction->attendance->update([
                'status' => $correction->requested_status,
                'time_in' => $correction->requested_time_in ?? $correction->attendance->time_in,
                'time_out' => $correction->requested_time_out ?? $correction->attendance->time_out,
                'notes' => 'Koreksi disetujui: ' . $correction->reason,
            ]);

            return back()->with('success', 'Koreksi absensi berhasil disetujui.');
        } else {
            $correction->update([
                'status' => 'REJECTED',
                'approved_by' => Auth::id(),
                'approved_at' => now(),
            ]);

            return back()->with('info', 'Koreksi absensi telah ditolak.');
        }
    }
}
