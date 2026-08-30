<?php

namespace App\Http\Controllers;

use App\Models\Citizen;
use App\Models\Service;
use App\Models\ServiceRequest;
use App\Models\Village;
use App\Services\Audit\AuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ServiceController extends Controller
{
    public function index()
    {
        $services = Service::where('is_active', true)->get();
        $requests = ServiceRequest::with('service', 'citizen')->latest()->paginate(15);
        $citizens = Citizen::all();

        return view('services.index', compact('services', 'requests', 'citizens'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'service_id' => 'required|exists:services,id',
            'citizen_id' => 'nullable|exists:citizens,id',
            'applicant_name' => 'required|string|max:150',
            'applicant_nik' => 'nullable|digits:16',
            'applicant_phone' => 'nullable|string|max:25',
            'notes' => 'nullable|string',
        ]);

        $village = Village::first();
        $validated['village_id'] = $village->uuid ?? Str::uuid()->toString();
        $validated['status'] = 'SUBMITTED';

        $req = ServiceRequest::create($validated);

        AuditService::log('CREATE', 'service_requests', $req->uuid, null, $req->toArray());

        return back()->with('success', 'Permohonan layanan berhasil didaftarkan.');
    }

    public function updateStatus(Request $request, $uuid)
    {
        $req = ServiceRequest::where('uuid', $uuid)->firstOrFail();
        $status = $request->input('status');

        $req->update([
            'status' => $status,
            'processed_by' => Auth::id(),
            'processed_at' => now(),
        ]);

        AuditService::log('UPDATE', 'service_requests', $req->uuid, null, ['status' => $status]);

        return back()->with('success', "Status layanan diubah menjadi {$status}.");
    }
}
