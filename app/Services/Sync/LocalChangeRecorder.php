<?php

namespace App\Services\Sync;

use App\Models\SyncQueue;
use Illuminate\Support\Str;

class LocalChangeRecorder
{
    public function record(string $table, string $recordUuid, string $operation, int $version, array $payload): SyncQueue
    {
        return SyncQueue::create([
            'sync_event_uuid' => (string) Str::uuid(),
            'village_uuid' => session('village_uuid') ?: config('siapdesa.village_uuid'),
            'device_uuid' => session('device_uuid') ?: config('siapdesa.device_uuid'),
            'table_name' => $table,
            'record_uuid' => $recordUuid,
            'operation' => strtoupper($operation),
            'record_version' => $version,
            'payload' => $payload,
            'status' => 'PENDING',
        ]);
    }
}
