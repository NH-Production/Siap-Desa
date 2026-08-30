<?php

namespace App\Models;

use App\Traits\Syncable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
    use Syncable, SoftDeletes;

    protected $guarded = ['id'];

    protected $casts = [
        'join_date' => 'date',
        'is_active' => 'boolean',
    ];

    public function citizen()
    {
        return $this->belongsTo(Citizen::class);
    }

    public function positions()
    {
        return $this->hasMany(EmployeePosition::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }
}
