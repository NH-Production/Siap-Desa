<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;

class FinanceTransactionItem extends Model
{
    use HasUuid;

    protected $guarded = ['id'];

    public function transaction()
    {
        return $this->belongsTo(FinanceTransaction::class);
    }
}
