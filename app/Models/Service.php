<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasUuid, \App\Traits\Syncable;

    protected $guarded = ['id'];

    protected $casts = [
        'requirements' => 'array',
        'is_active' => 'boolean',
    ];

    public function requests()
    {
        return $this->hasMany(ServiceRequest::class);
    }
}
