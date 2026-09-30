<?php

namespace App\Http\Controllers;

use App\Models\Citizen;
use App\Models\Letter;
use App\Models\LetterType;
use App\Models\Village;
use App\Services\Audit\AuditService;
use App\Services\QrCode\QrService;
use Carbon\Carbon;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class LetterController extends Controller
{
    public function index(Request $request)
    {
        $query = Letter::with('letterType', 'citizen', 'approvedByUser');

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('letter_number', 'like', "%{$search}%")
                  ->orWhere('applicant_name', 'like', "%{$search}%")
                  ->orWhere('applicant_nik', 'like', "%{$search}%");
            });
        }

        if ($type = $request->input('type')) {
            $query->where('letter_type_id', $type);
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $letters = $query->latest()->paginate(15)->withQueryString();
        $letterTypes = LetterType::where('is_active', true)->get();

        return view('letters.index', compact('letters', 'letterTypes'));
    }

    public function create(Request $request)
    {
        $typeId = $request->input('type_id');
        $selectedType = $typeId ? LetterType::findOrFail($typeId) : LetterType::first();
        $letterTypes = LetterType::where('is_active', true)->get();
        $citizens = Citizen::all();

        return view('letters.create', compact('letterTypes', 'selectedType', 'citizens'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'letter_type_id' => 'required|exists:letter_types,id',
            'citizen_id' => 'nullable|exists:citizens,id',
            'applicant_name' => 'required|string|max:150',
            'applicant_nik' => 'nullable|digits:16',
            'applicant_address' => 'nullable|string',
            'purpose' => 'nullable|string',
            'letter_data' => 'nullable|array',
        ]);

        $village = Village::first();
        $letterType = LetterType::findOrFail($validated['letter_type_id']);

        // Generate draft number
        $draftNum = 'DRAFT-' . date('Ymd') . '-' . strtoupper(Str::random(6));

        $letter = Letter::create([
            'village_id' => $village->uuid ?? Str::uuid()->toString(),
            'letter_type_id' => $letterType->id,
            'draft_number' => $draftNum,
            'citizen_id' => $validated['citizen_id'] ?? null,
            'applicant_name' => $validated['applicant_name'],
            'applicant_nik' => $validated['applicant_nik'] ?? null,
            'applicant_address' => $validated['applicant_address'] ?? null,
            'purpose' => $validated['purpose'] ?? null,
            'letter_data' => $validated['letter_data'] ?? [],
            'status' => 'DRAFT',
            'qr_verification_token' => 'VERIF-' . strtoupper(Str::random(12)),
        ]);

        AuditService::log('CREATE', 'letters', $letter->uuid, null, $letter->toArray());

        return redirect()->route('letters.show', $letter->uuid)->with('success', 'Draft surat berhasil dibuat.');
    }

    public function show($uuid)
    {
        $letter = Letter::where('uuid', $uuid)->with('letterType', 'citizen', 'approvedByUser')->firstOrFail();
        $village = Village::first();

        return view('letters.show', compact('letter', 'village'));
    }

    public function approveLetter(Request $request, $uuid)
    {
        $letter = Letter::where('uuid', $uuid)->firstOrFail();
        $village = Village::first();

        // Generate official letter number
        $monthRoman = ['I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII'][date('n') - 1];
        $countThisYear = Letter::whereYear('created_at', date('Y'))->whereNotNull('letter_number')->count() + 1;
        $formattedCount = str_pad($countThisYear, 3, '0', STR_PAD_LEFT);

        $format = $letter->letterType->number_format ?? $village->letter_number_format ?? '{KODE}/{NO}/{BULAN_ROMAWI}/{TAHUN}';
        $letterNumber = str_replace(
            ['{KODE}', '{NO}', '{BULAN_ROMAWI}', '{TAHUN}'],
            [$letter->letterType->code, $formattedCount, $monthRoman, date('Y')],
            $format
        );

        $letter->update([
            'letter_number' => $letterNumber,
            'status' => 'APPROVED',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
            'issued_at' => Carbon::today(),
        ]);

        AuditService::log('APPROVE', 'letters', $letter->uuid, null, ['letter_number' => $letterNumber]);

        return back()->with('success', "Surat berhasil disetujui dengan nomor resmi: {$letterNumber}");
    }

    public function printLetter($uuid)
    {
        $letter = Letter::where('uuid', $uuid)->with('letterType', 'citizen', 'approvedByUser')->firstOrFail();
        $village = Village::first();

        $qrService = new QrService();
        $qrDataUri = $qrService->generateDataUri($letter->qr_verification_token ?? $letter->uuid, 120);

        AuditService::log('PRINT', 'letters', $letter->uuid);

        return view('letters.print', compact('letter', 'village', 'qrDataUri'));
    }

    public function verify(string $token)
    {
        $letter = Letter::with('letterType')->where('qr_verification_token', $token)->where('status','APPROVED')->first();
        if (!$letter) abort(404);
        return view('letters.verify', compact('letter'));
    }

    public function destroy($uuid)
    {
        $letter = Letter::where('uuid', $uuid)->firstOrFail();
        $letter->delete();

        AuditService::log('DELETE', 'letters', $letter->uuid);

        return redirect()->route('letters.index')->with('success', 'Surat telah dihapus.');
    }
}
