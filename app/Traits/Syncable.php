<?php

namespace App\Traits;

use App\Services\Sync\LocalChangeRecorder;

trait Syncable
{
    protected static function bootSyncable(): void
    {
        static::creating(function ($model) {
            if (!$model->uuid) $model->uuid = (string) \Illuminate\Support\Str::uuid();
            $model->version = 1;
        });

        static::created(function ($model) {
            app(LocalChangeRecorder::class)->record(
                $model->getTable(), $model->uuid, 'CREATE', (int) $model->version, $model->getAttributes()
            );
        });

        static::updating(function ($model) {
            $model->version = ((int) $model->getOriginal('version')) + 1;
        });

        static::updated(function ($model) {
            app(LocalChangeRecorder::class)->record(
                $model->getTable(), $model->uuid, 'UPDATE', (int) $model->version, $model->getAttributes()
            );
        });

        static::deleted(function ($model) {
            $model->version = ((int) $model->version) + 1;
            app(LocalChangeRecorder::class)->record(
                $model->getTable(), $model->uuid, 'DELETE', (int) $model->version, $model->getAttributes()
            );
        });
    }
}
