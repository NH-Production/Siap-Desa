<?php

namespace App\Models;

use App\Traits\HasUuid;
use App\Traits\Syncable;
use Illuminate\Database\Eloquent\Model;

class AidProgram extends Model
{
    use HasUuid, Syncable;

    protected $guarded = ['id'];

    public function recipients()
    {
        return $this->hasMany(AidRecipient::class);
    }
}
