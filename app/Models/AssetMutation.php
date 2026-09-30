<?php

namespace App\Models;

use App\Traits\HasUuid;
use App\Traits\Syncable;
use Illuminate\Database\Eloquent\Model;

class AssetMutation extends Model
{
    use HasUuid, Syncable;

    protected $guarded = ['id'];

    protected $casts = [
        'date' => 'date',
    ];

    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }
}
