<?php
namespace App\Models;
use App\Traits\Syncable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Spj extends Model {
 use Syncable, SoftDeletes;
 protected $table='spj'; protected $guarded=['id'];
 protected $casts=['date'=>'date','verified_at'=>'datetime'];
 public function transaction(){return $this->belongsTo(FinanceTransaction::class);}
}
