<?php
namespace Database\Seeders;
use App\Models\LetterType;
use App\Models\Service;
use Illuminate\Database\Seeder;

class LetterAndServiceSeeder extends Seeder
{
 public function run(): void {
  foreach([
   ['SKTM','Surat Keterangan Tidak Mampu','SKTM/{NO}/{BULAN_ROMAWI}/{TAHUN}'],
   ['SKU','Surat Keterangan Usaha','SKU/{NO}/{BULAN_ROMAWI}/{TAHUN}'],
   ['SKDOM','Surat Keterangan Domisili','SKDOM/{NO}/{BULAN_ROMAWI}/{TAHUN}'],
   ['SKCK','Surat Pengantar SKCK','SKCK/{NO}/{BULAN_ROMAWI}/{TAHUN}'],
   ['SKL','Surat Keterangan Kelahiran','SKL/{NO}/{BULAN_ROMAWI}/{TAHUN}'],
   ['SKM','Surat Keterangan Kematian','SKM/{NO}/{BULAN_ROMAWI}/{TAHUN}'],
  ] as [$code,$name,$format]) LetterType::updateOrCreate(['code'=>$code],['name'=>$name,'number_format'=>$format,'fields_schema'=>['purpose','notes'],'is_active'=>true]);
  foreach([
   ['ADM','Administrasi Umum',['KTP/KK','Formulir Permohonan']],
   ['DOM','Pelayanan Domisili',['KTP','KK']],
   ['USAHA','Surat Keterangan Usaha',['KTP','Keterangan Usaha']],
  ] as [$code,$name,$requirements]) Service::updateOrCreate(['code'=>$code],['name'=>$name,'requirements'=>$requirements,'is_active'=>true]);
 }
}