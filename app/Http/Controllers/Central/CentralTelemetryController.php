<?php

namespace App\Http\Controllers\Central;

use App\Http\Controllers\Controller;
use App\Models\CentralSyncLog;
use Illuminate\Http\Request;

class CentralTelemetryController extends Controller
{
    public function index(Request $request)
    {
        $query = CentralSyncLog::latest();
        if ($request->filled('direction')) {
            $query->where('direction', $request->direction);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        $logs = $query->paginate(25);
        return view('central.telemetry', compact('logs'));
    }
}
