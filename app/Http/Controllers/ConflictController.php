<?php

namespace App\Http\Controllers;

use App\Models\SyncConflict;
use App\Services\Audit\AuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ConflictController extends Controller
{
    public function index()
    {
        $conflicts = SyncConflict::with('resolvedByUser')->latest()->paginate(15);
        return view('sync.conflicts', compact('conflicts'));
    }

    public function resolve(Request $request, $uuid)
    {
        $conflict = SyncConflict::where('uuid', $uuid)->firstOrFail();
        $resolution = $request->input('resolution'); // SERVER_WINS, LOCAL_WINS, MERGED

        $conflict->update([
            'resolution' => $resolution,
            'resolved_by' => Auth::id(),
            'resolved_at' => now(),
        ]);

        AuditService::log('CONFLICT_RESOLVE', 'sync_conflicts', $conflict->uuid, null, ['resolution' => $resolution]);

        return back()->with('success', "Konflik berhasil diselesaikan menggunakan metode: {$resolution}.");
    }
}
