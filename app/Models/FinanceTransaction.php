<?php

namespace App\Models;

use App\Traits\Syncable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FinanceTransaction extends Model
{
    use Syncable, SoftDeletes;

    protected $guarded = ['id'];

    protected $casts = [
        'transaction_date' => 'date',
        'amount' => 'decimal:2',
    ];

    public function account()
    {
        return $this->belongsTo(FinanceAccount::class);
    }

    public function items()
    {
        return $this->hasMany(FinanceTransactionItem::class, 'transaction_id');
    }

    public function evidences()
    {
        return $this->hasMany(FinanceEvidence::class, 'transaction_id');
    }
}
