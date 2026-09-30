<?php

namespace App\Models;

use App\Traits\HasUuid;
use App\Traits\Syncable;
use Illuminate\Database\Eloquent\Model;

class FinanceAccount extends Model
{
    use HasUuid, Syncable;

    protected $guarded = ['id'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function parent()
    {
        return $this->belongsTo(FinanceAccount::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(FinanceAccount::class, 'parent_id');
    }

    public function transactions()
    {
        return $this->hasMany(FinanceTransaction::class, 'account_id');
    }
}
