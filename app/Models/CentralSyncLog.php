<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;

class CentralSyncLog extends Model
{
    use HasUuid;

    protected $guarded = ['id'];
}
