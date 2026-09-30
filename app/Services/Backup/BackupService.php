<?php
namespace App\Services\Backup;
use App\Models\Backup;
use App\Services\Audit\AuditService;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;

class BackupService {
 public function createBackup(string $type='MANUAL'): Backup {
  $dir=config('siapdesa.data_path',storage_path('app')).DIRECTORY_SEPARATOR.'backups';
  if(!File::isDirectory($dir)) File::makeDirectory($dir,0755,true,true);
  $timestamp=Carbon::now()->format('Y-m-d_His');
  $filename='siap_desa_backup_'.$timestamp.'.sql';
  $dest=$dir.DIRECTORY_SEPARATOR.$filename;
  $this->dumpMysql($dest);
  $size=File::exists($dest)?File::size($dest):0; $checksum=File::exists($dest)?hash_file('sha256',$dest):null;
  $backup=Backup::create(['filename'=>$filename,'file_path'=>$dest,'size_bytes'=>$size,'type'=>$type,'status'=>File::exists($dest)?'SUCCESS':'FAILED','checksum'=>$checksum]);
  AuditService::log('BACKUP','backups',(string)$backup->id,null,['filename'=>$filename,'size'=>$size,'checksum'=>$checksum]);
  return $backup;
 }
 private function dumpMysql(string $dest):void {
  $c=config('database.connections.mysql');
  $mysqldump=env('MYSQLDUMP_PATH','mysqldump');
  $cmd=escapeshellarg($mysqldump).' --host='.escapeshellarg($c['host']).' --port='.escapeshellarg((string)$c['port']).' --user='.escapeshellarg($c['username']).' --single-transaction --routines --triggers '.escapeshellarg($c['database']);
  if(!empty($c['password'])) $cmd='set MYSQL_PWD='.escapeshellarg($c['password']).'&& '.$cmd;
  $cmd.=' > '.escapeshellarg($dest).' 2> '.escapeshellarg($dest.'.err');
  exec($cmd,$out,$code);
  if($code!==0){$err=File::exists($dest.'.err')?File::get($dest.'.err'):'mysqldump failed'; @File::delete($dest); throw new \RuntimeException($err);}
  @File::delete($dest.'.err');
 }
}