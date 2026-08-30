<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;

class CentralDevice extends Model
{
    use HasUuid;

    protected $guarded = ['id'];

    protected $casts = [
        'last_seen_at' => 'datetime',
    ];

    public function license()
    {
        return $this->belongsTo(CentralLicense::class, 'central_license_id');
    }
}
