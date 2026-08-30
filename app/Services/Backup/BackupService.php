<?php

namespace App\Services\Backup;

use App\Models\Backup;
use App\Services\Audit\AuditService;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;

class BackupService
{
    public function createBackup(string $type = 'MANUAL'): Backup
    {
        $backupDir = config('siap.data_path', 'C:\ProgramData\SIAP Desa') . DIRECTORY_SEPARATOR . 'backups';
        if (!File::isDirectory($backupDir)) {
            File::makeDirectory($backupDir, 0755, true, true);
        }

        $timestamp = Carbon::now()->format('Y-m-d_His');
        $filename = 'siap_desa_backup_' . $timestamp . '.sqlite';
        $destPath = $backupDir . DIRECTORY_SEPARATOR . $filename;

        $sourceDb = config('database.connections.sqlite.database');
        if (File::exists($sourceDb)) {
            File::copy($sourceDb, $destPath);
        }

        $size = File::exists($destPath) ? File::size($destPath) : 0;
        $checksum = File::exists($destPath) ? hash_file('sha256', $destPath) : null;

        $backup = Backup::create([
            'filename' => $filename,
            'file_path' => $destPath,
            'size_bytes' => $size,
            'type' => $type,
            'status' => 'SUCCESS',
            'checksum' => $checksum,
        ]);

        AuditService::log('BACKUP', 'backups', (string)$backup->id, null, ['filename' => $filename, 'size' => $size]);

        return $backup;
    }
}
