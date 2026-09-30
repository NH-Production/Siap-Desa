<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;

class LetterType extends Model
{
    use HasUuid, \App\Traits\Syncable;

    protected $guarded = ['id'];

    protected $casts = [
        'fields_schema' => 'array',
        'is_active' => 'boolean',
    ];

    public function letters()
    {
        return $this->hasMany(Letter::class);
    }
}
