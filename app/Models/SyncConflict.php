<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;

class SyncConflict extends Model
{
    use HasUuid;

    protected $guarded = ['id'];

    protected $casts = [
        'server_payload' => 'array',
        'local_payload' => 'array',
        'resolved_at' => 'datetime',
    ];

    public function resolvedByUser()
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }
}
