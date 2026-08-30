<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SyncCursor extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'last_sync_at' => 'datetime',
    ];
}
