<?php

namespace App\Models;

use App\Traits\Syncable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Attendance extends Model
{
    use Syncable, SoftDeletes;
    protected $table = 'attendance';
    protected $guarded = ['id'];
    protected $casts = ['date'=>'date'];
    public function employee(){ return $this->belongsTo(Employee::class); }
    public function corrections(){ return $this->hasMany(AttendanceCorrection::class); }
}
