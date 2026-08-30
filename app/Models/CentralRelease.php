<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;

class CentralRelease extends Model
{
    use HasUuid;

    protected $guarded = ['id'];

    protected $casts = [
        'release_date' => 'date',
        'is_mandatory' => 'boolean',
        'is_published' => 'boolean',
    ];
}
