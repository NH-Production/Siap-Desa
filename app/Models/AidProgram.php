<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;

class AidProgram extends Model
{
    use HasUuid;

    protected $guarded = ['id'];

    public function recipients()
    {
        return $this->hasMany(AidRecipient::class);
    }
}
