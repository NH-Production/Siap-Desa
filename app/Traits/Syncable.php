<?php

namespace App\Traits;

use App\Models\SyncQueue;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

trait Syncable
{
    use HasUuid;

    public static function bootSyncable(): void
    {
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
            if (empty($model->version)) {
                $model->version = 1;
            }
            if (Auth::check() && empty($model->last_modified_by)) {
                $model->last_modified_by = Auth::user()->uuid ?? null;
            }
        });

        static::created(function ($model) {
            $model->queueSync('INSERT');
        });

        static::updating(function ($model) {
            $model->version = ($model->version ?? 0) + 1;
            if (Auth::check()) {
                $model->last_modified_by = Auth::user()->uuid ?? null;
            }
        });

        static::updated(function ($model) {
            $model->queueSync('UPDATE');
        });

        static::deleted(function ($model) {
            $model->queueSync('DELETE');
        });
    }

    public function queueSync(string $operation): void
    {
        try {
            if (!config('siap.sync_enabled', true)) {
                return;
            }

            SyncQueue::create([
                'device_id' => session('device_id'),
                'village_id' => $this->village_id ?? session('village_id') ?? Str::uuid()->toString(),
                'table_name' => $this->getTable(),
                'record_uuid' => $this->uuid,
                'operation' => $operation,
                'payload' => $this->toArray(),
                'base_version' => max(1, ($this->version ?? 1) - 1),
                'local_version' => $this->version ?? 1,
                'status' => 'PENDING',
                'attempts' => 0,
            ]);
        } catch (\Throwable $e) {
            // Avoid failing local operation if sync queue encounters temporary issue
        }
    }
}
