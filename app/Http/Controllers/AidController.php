<?php

namespace App\Http\Controllers;

use App\Models\AidProgram;
use App\Models\AidRecipient;
use App\Models\Citizen;
use App\Models\Village;
use App\Services\Audit\AuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AidController extends Controller
{
    public function index()
    {
        $programs = AidProgram::with('recipients.citizen')->latest()->get();
        $citizens = Citizen::all();

        return view('aid.index', compact('programs', 'citizens'));
    }

    public function storeProgram(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'year' => 'required|integer',
            'budget_per_recipient' => 'required|numeric|min:0',
            'quota' => 'required|integer|min:1',
            'source' => 'required|string',
            'description' => 'nullable|string',
        ]);

        $village = Village::first();
        $validated['village_id'] = $village->uuid ?? Str::uuid()->toString();

        $prog = AidProgram::create($validated);

        AuditService::log('CREATE', 'aid_programs', $prog->uuid, null, $prog->toArray());

        return back()->with('success', 'Program Bantuan Sosial berhasil ditambahkan.');
    }

    public function storeRecipient(Request $request)
    {
        $validated = $request->validate([
            'aid_program_id' => 'required|exists:aid_programs,id',
            'citizen_id' => 'required|exists:citizens,id',
            'notes' => 'nullable|string',
        ]);

        $program = AidProgram::findOrFail($validated['aid_program_id']);

        AidRecipient::firstOrCreate(
            [
                'aid_program_id' => $program->id,
                'citizen_id' => $validated['citizen_id'],
            ],
            [
                'amount_received' => $program->budget_per_recipient,
                'status' => 'TERVERIFIKASI',
                'notes' => $validated['notes'],
            ]
        );

        return back()->with('success', 'Penerima bantuan berhasil didaftarkan.');
    }

    public function markDistributed(Request $request, $uuid)
    {
        $recipient = AidRecipient::where('uuid', $uuid)->firstOrFail();
        $recipient->update([
            'status' => 'DISALURKAN',
            'distribution_date' => now()->toDateString(),
        ]);

        AuditService::log('UPDATE', 'aid_recipients', $recipient->uuid, null, ['status' => 'DISALURKAN']);

        return back()->with('success', 'Penyaluran bantuan berhasil dicatat.');
    }
}
