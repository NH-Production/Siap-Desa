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
        'registered_at' => 'datetime',
        'revoked_at' => 'datetime',
    ];
}
