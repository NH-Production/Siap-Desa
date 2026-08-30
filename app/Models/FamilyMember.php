<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;

class FamilyMember extends Model
{
    use HasUuid;

    protected $guarded = ['id'];

    public function family()
    {
        return $this->belongsTo(Family::class);
    }

    public function citizen()
    {
        return $this->belongsTo(Citizen::class);
    }
}
