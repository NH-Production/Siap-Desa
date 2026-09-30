<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    use HasUuid;

    protected $guarded = ['id'];

    protected $casts = [
        'last_seen_at' => 'datetime',
        'last_sync_at' => 'datetime',
        'registered_at' => 'datetime',
        'revoked_at' => 'datetime',
    ];

    public function village()
    {
        return $this->belongsTo(Village::class, 'village_uuid', 'uuid');
    }

    public function getConnectionStatusAttribute(): string
    {
        if ($this->status !== 'active') {
            return strtoupper($this->status);
        }

        if (!$this->last_seen_at) {
            return 'OFFLINE';
        }

        return $this->last_seen_at->gte(now()->subMinutes(3))
            ? 'ONLINE'
            : 'OFFLINE';
    }
}
