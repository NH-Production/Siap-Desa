<?php

namespace App\Models;

use App\Traits\HasUuid;
use App\Traits\Syncable;
use Illuminate\Database\Eloquent\Model;

class FinanceEvidence extends Model
{
    use HasUuid, Syncable;

    protected $table = 'finance_evidences';
    protected $guarded = ['id'];

    public function transaction()
    {
        return $this->belongsTo(FinanceTransaction::class);
    }
}
