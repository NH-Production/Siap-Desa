<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CentralVillage extends Model
{
    use HasUuid, SoftDeletes;

    protected $guarded = ['id'];

    public function licenses()
    {
        return $this->hasMany(CentralLicense::class, 'central_village_id');
    }

    public function activeLicense()
    {
        return $this->hasOne(CentralLicense::class, 'central_village_id')->where('status', 'ACTIVE')->latest();
    }
}
