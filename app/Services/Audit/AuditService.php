<?php

namespace App\Services\Audit;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Str;

class AuditService
{
    public static function log(string $action, ?string $tableName = null, ?string $recordUuid = null, ?array $oldData = null, ?array $newData = null): void
    {
        try {
            // Mask sensitive fields if present
            $sensitiveKeys = ['password', 'remember_token', 'token', 'secret', 'qr_token'];
            if ($oldData) {
                foreach ($sensitiveKeys as $key) {
                    if (isset($oldData[$key])) $oldData[$key] = '***MASKED***';
                }
            }
            if ($newData) {
                foreach ($sensitiveKeys as $key) {
                    if (isset($newData[$key])) $newData[$key] = '***MASKED***';
                }
            }

            AuditLog::create([
                'uuid' => (string) Str::uuid(),
                'user_id' => Auth::id(),
                'device_id' => session('device_id'),
                'village_id' => session('village_id') ?? Auth::user()->village_id ?? null,
                'action' => strtoupper($action),
                'table_name' => $tableName,
                'record_uuid' => $recordUuid,
                'old_data' => $oldData,
                'new_data' => $newData,
                'ip_address' => Request::ip(),
                'user_agent' => Request::userAgent(),
            ]);
        } catch (\Throwable $e) {
            // Silently fail to avoid crashing primary transaction
        }
    }
}
