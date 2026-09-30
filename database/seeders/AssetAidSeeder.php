<?php
namespace Database\Seeders;
use App\Models\AssetCategory;use App\Models\AidProgram;use App\Models\Village;use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
class AssetAidSeeder extends Seeder{
 public function run():void{
  foreach([['ELEK','Peralatan Elektronik'],['MEUB','Mebel/Furnitur'],['KEND','Kendaraan'],['BANG','Bangunan'],['TANAH','Tanah/Aset Tetap Lainnya']] as [$code,$name]) AssetCategory::updateOrCreate(['code'=>$code],['name'=>$name,'is_active'=>true]);
  $v=Village::first(); if($v) AidProgram::updateOrCreate(['code'=>'BLT-DD-'.date('Y')],['village_id'=>$v->uuid,'name'=>'BLT Dana Desa '.date('Y'),'year'=>date('Y'),'source'=>'Dana Desa','budget_per_recipient'=>0,'quota'=>0,'status'=>'DRAFT']);
 }
}