<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;

class CentralLicense extends Model
{
    use HasUuid;

    protected $guarded = ['id'];

    protected $casts = [
        'issued_date' => 'date',
        'expiry_date' => 'date',
    ];

    public function village()
    {
        return $this->belongsTo(CentralVillage::class, 'central_village_id');
    }

    public function devices()
    {
        return $this->hasMany(CentralDevice::class, 'central_license_id');
    }
}
