<?php

namespace App\Services\Diagnostic;

use App\Models\Device;
use App\Models\SyncConflict;
use App\Models\SyncQueue;
use App\Models\SystemVersion;
use App\Models\Village;
use Illuminate\Support\Facades\DB;

class DiagnosticService
{
    public function getSystemHealth(): array
    {
        $dbStatus = 'OK';
        $dbError = null;
        try {
            DB::connection()->getPdo();
        } catch (\Throwable $e) {
            $dbStatus = 'ERROR';
            $dbError = $e->getMessage();
        }

        $version = SystemVersion::orderBy('id', 'desc')->first();
        $village = Village::first();
        $device = Device::first();

        $pendingPush = SyncQueue::where('status', 'PENDING')->count();
        $failedPush = SyncQueue::where('status', 'FAILED')->count();
        $conflicts = SyncConflict::whereNull('resolved_at')->count();

        // Disk space
        $dataPath = config('siap.data_path', 'C:\ProgramData\SIAP Desa');
        $freeSpace = @disk_free_space($dataPath);
        $totalSpace = @disk_total_space($dataPath);
        $freeSpaceGb = $freeSpace ? round($freeSpace / (1024 * 1024 * 1024), 2) : 0;
        $totalSpaceGb = $totalSpace ? round($totalSpace / (1024 * 1024 * 1024), 2) : 0;

        return [
            'app_version' => $version->app_version ?? '1.0.0',
            'schema_version' => $version->schema_version ?? 1,
            'sync_protocol_version' => $version->sync_protocol_version ?? 1,
            'php_version' => PHP_VERSION,
            'db_driver' => config('database.default'),
            'db_status' => $dbStatus,
            'db_error' => $dbError,
            'village_name' => $village->name ?? 'Belum Dikonfigurasi',
            'device_code' => $device->device_code ?? 'PC-DEFAULT',
            'device_status' => $device->status ?? 'ACTIVE',
            'pending_push' => $pendingPush,
            'failed_push' => $failedPush,
            'conflicts' => $conflicts,
            'disk_free_gb' => $freeSpaceGb,
            'disk_total_gb' => $totalSpaceGb,
            'data_path' => $dataPath,
        ];
    }
}
