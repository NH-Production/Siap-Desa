<?php

namespace App\Http\Controllers;

use App\Models\Backup;
use App\Services\Backup\BackupService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class BackupController extends Controller
{
    public function index()
    {
        $backups = Backup::latest()->paginate(15);
        return view('backups.index', compact('backups'));
    }

    public function createBackup(BackupService $service)
    {
        $backup = $service->createBackup('MANUAL');
        return back()->with('success', "Backup database berhasil dibuat: {$backup->filename}");
    }

    public function downloadBackup($id)
    {
        $backup = Backup::findOrFail($id);
        if (File::exists($backup->file_path)) {
            return response()->download($backup->file_path);
        }
        return back()->with('error', 'File backup tidak ditemukan pada storage lokal.');
    }

    public function restoreBackup(Request $request)
    {
        $id = $request->input('backup_id');
        $backup = Backup::findOrFail($id);

        if (!File::exists($backup->file_path)) {
            return back()->with('error', 'File backup fisik tidak ditemukan.');
        }

        $destDb = config('database.connections.sqlite.database');
        File::copy($backup->file_path, $destDb);

        return back()->with('success', "Database lokal berhasil dipulihkan dari backup: {$backup->filename}");
    }
}
