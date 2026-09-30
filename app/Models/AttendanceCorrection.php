<?php

namespace App\Models;

use App\Traits\Syncable;
use Illuminate\Database\Eloquent\Model;

class AttendanceCorrection extends Model
{
    use Syncable;

    protected $guarded = ['id'];

    protected $casts = [
        'approved_at' => 'datetime',
    ];

    public function attendance(){ return $this->belongsTo(Attendance::class); }
    public function employee(){ return $this->belongsTo(Employee::class); }
}
