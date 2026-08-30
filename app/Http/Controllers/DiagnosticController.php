<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Services\Diagnostic\DiagnosticService;
use Illuminate\Http\Request;

class DiagnosticController extends Controller
{
    public function index(DiagnosticService $service)
    {
        $health = $service->getSystemHealth();
        return view('system.diagnostics', compact('health'));
    }

    public function auditLogs(Request $request)
    {
        $query = AuditLog::with('user');

        if ($action = $request->input('action')) {
            $query->where('action', $action);
        }

        if ($table = $request->input('table_name')) {
            $query->where('table_name', $table);
        }

        $logs = $query->latest()->paginate(25)->withQueryString();

        return view('system.audit-logs', compact('logs'));
    }
}
