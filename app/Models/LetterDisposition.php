<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;

class LetterDisposition extends Model
{
    use HasUuid;

    protected $guarded = ['id'];

    public function letter()
    {
        return $this->belongsTo(Letter::class);
    }
}
