<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\SyncConflict;
use App\Models\SyncQueue;
use App\Services\Audit\AuditService;
use App\Services\Sync\SyncEngine;
use Illuminate\Http\Request;

class SyncController extends Controller
{
    public function index()
    {
        $pendingItems = SyncQueue::where('status', 'PENDING')->latest()->paginate(15);
        $totalPending = SyncQueue::where('status', 'PENDING')->count();
        $totalSynced = SyncQueue::where('status', 'SYNCED')->count();
        $totalFailed = SyncQueue::where('status', 'FAILED')->count();
        $totalConflicts = SyncConflict::whereNull('resolved_at')->count();
        $device = Device::first();

        return view('sync.index', compact('pendingItems', 'totalPending', 'totalSynced', 'totalFailed', 'totalConflicts', 'device'));
    }

    public function triggerPush(SyncEngine $engine)
    {
        $result = $engine->pushChanges();
        return back()->with('success', "Proses Push selesai: {$result['pushed']} perubahan berhasil dikirim.");
    }

    public function triggerPull(SyncEngine $engine)
    {
        $result = $engine->pullChanges();
        return back()->with('success', "Proses Pull selesai: Data lokal telah diperbarui.");
    }

    public function triggerFullSync(SyncEngine $engine)
    {
        $push = $engine->pushChanges();
        $pull = $engine->pullChanges();

        return back()->with('success', "Sinkronisasi Dua Arah Selesai (Push: {$push['pushed']}, Pull: {$pull['pulled']}).");
    }
}
