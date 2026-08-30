<?php

namespace App\Models;

use App\Traits\Syncable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Citizen extends Model
{
    use Syncable, SoftDeletes;

    protected $guarded = ['id'];

    protected $casts = [
        'birth_date' => 'date',
    ];

    public function familyMember()
    {
        return $this->hasOne(FamilyMember::class);
    }

    public function employee()
    {
        return $this->hasOne(Employee::class);
    }
}
