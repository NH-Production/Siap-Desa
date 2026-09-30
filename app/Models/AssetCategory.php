<?php

namespace App\Models;

use App\Traits\HasUuid;
use App\Traits\Syncable;
use Illuminate\Database\Eloquent\Model;

class AssetCategory extends Model
{
    use HasUuid, Syncable;

    protected $guarded = ['id'];

    public function assets()
    {
        return $this->hasMany(Asset::class, 'category_id');
    }
}
