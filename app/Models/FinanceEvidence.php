<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;

class FinanceEvidence extends Model
{
    use HasUuid;

    protected $table = 'finance_evidence';
    protected $guarded = ['id'];

    public function transaction()
    {
        return $this->belongsTo(FinanceTransaction::class);
    }
}
