<?php
namespace App\Http\Controllers;
use App\Models\Backup;
use App\Services\Backup\BackupService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
class BackupController extends Controller {
 public function index(){return view('backups.index',['backups'=>Backup::latest()->paginate(15)]);}
 public function createBackup(BackupService $service){try{$b=$service->createBackup('MANUAL');return back()->with('success',"Backup database berhasil dibuat: {$b->filename}");}catch(\Throwable $e){return back()->with('error','Backup gagal: '.$e->getMessage());}}
 public function downloadBackup($id){$b=Backup::findOrFail($id);abort_unless(File::exists($b->file_path),404);return response()->download($b->file_path);}
 public function restoreBackup(Request $request){
  $b=Backup::findOrFail($request->input('backup_id')); abort_unless(File::exists($b->file_path),404);
  abort_unless(auth()->user()?->hasPermission('system.backup.restore'),403);
  $c=config('database.connections.mysql'); $mysql=env('MYSQL_PATH','mysql');
  $cmd=escapeshellarg($mysql).' --host='.escapeshellarg($c['host']).' --port='.escapeshellarg((string)$c['port']).' --user='.escapeshellarg($c['username']).' '.escapeshellarg($c['database']).' < '.escapeshellarg($b->file_path).' 2>&1';
  if(!empty($c['password'])) $cmd='set MYSQL_PWD='.escapeshellarg($c['password']).'&& '.$cmd;
  exec($cmd,$out,$code); if($code!==0)return back()->with('error','Restore gagal: '.implode("\n",$out));
  return back()->with('success','Database berhasil dipulihkan dari backup SQL.');
 }
}