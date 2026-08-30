<?php

namespace App\Models;

use App\Traits\Syncable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ServiceRequest extends Model
{
    use Syncable, SoftDeletes;

    protected $guarded = ['id'];

    protected $casts = [
        'request_data' => 'array',
        'processed_at' => 'datetime',
    ];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function citizen()
    {
        return $this->belongsTo(Citizen::class);
    }
}
