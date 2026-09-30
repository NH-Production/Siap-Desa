<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class SyncCheckpoint extends Model {
 protected $table='sync_checkpoints'; public $timestamps=false; protected $guarded=['id'];
 protected $casts=['last_push_at'=>'datetime','last_pull_at'=>'datetime','updated_at'=>'datetime'];
}