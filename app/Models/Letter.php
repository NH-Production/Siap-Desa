<?php

namespace App\Models;

use App\Traits\Syncable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Letter extends Model
{
    use Syncable, SoftDeletes;

    protected $guarded = ['id'];

    protected $casts = [
        'letter_data' => 'array',
        'issued_at' => 'date',
        'approved_at' => 'datetime',
    ];

    public function letterType()
    {
        return $this->belongsTo(LetterType::class);
    }

    public function citizen()
    {
        return $this->belongsTo(Citizen::class);
    }

    public function approvedByUser()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function dispositions()
    {
        return $this->hasMany(LetterDisposition::class);
    }
}
