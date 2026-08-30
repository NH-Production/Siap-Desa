<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;

class SyncQueue extends Model
{
    use HasUuid;

    protected $table = 'sync_queue';
    protected $guarded = ['id'];

    protected $casts = [
        'payload' => 'array',
        'last_attempt_at' => 'datetime',
        'synced_at' => 'datetime',
    ];
}
