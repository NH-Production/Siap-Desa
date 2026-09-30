<?php

namespace App\Models;

use App\Traits\HasUuid;
use App\Traits\Syncable;
use Illuminate\Database\Eloquent\Model;

class AidRecipient extends Model
{
    use HasUuid, Syncable;

    protected $guarded = ['id'];

    protected $casts = [
        'distribution_date' => 'date',
    ];

    public function program()
    {
        return $this->belongsTo(AidProgram::class, 'aid_program_id');
    }

    public function citizen()
    {
        return $this->belongsTo(Citizen::class);
    }
}
