<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Device;
use App\Models\SyncQueue;
use App\Models\SyncConflict;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CentralSyncApiController extends Controller
{
    public function push(Request $request)
    {
        $data=$request->validate([
            'protocol_version'=>'required|integer',
            'village_uuid'=>'required|uuid',
            'device_uuid'=>'required|uuid',
            'events'=>'array',
            'events.*.event_uuid'=>'required|uuid',
            'events.*.table'=>'required|string|max:120',
            'events.*.record_uuid'=>'required|uuid',
            'events.*.operation'=>'required|string|in:CREATE,INSERT,UPDATE,DELETE',
            'events.*.version'=>'required|integer|min:1',
            'events.*.payload'=>'nullable|array',
        ]);
        $device=Device::where('uuid',$data['device_uuid'])->where('village_uuid',$data['village_uuid'])->first();
        abort_unless($device && $device->status==='active',401,'Device tidak terdaftar atau diblokir.');
        $synced=[];$conflicts=[];
        foreach($data['events']??[] as $event){
            $existing=SyncQueue::where('sync_event_uuid',$event['event_uuid'])->first();
            if($existing){$synced[]=$event['event_uuid'];continue;}
            $latest=SyncQueue::where('village_uuid',$data['village_uuid'])->where('table_name',$event['table'])->where('record_uuid',$event['record_uuid'])->where('status','SYNCED')->max('record_version');
            if($latest!==null && (int)$event['version'] <= (int)$latest){
                $conflicts[]=['event_uuid'=>$event['event_uuid'],'table'=>$event['table'],'record_uuid'=>$event['record_uuid'],'local_version'=>$event['version'],'remote_version'=>$latest,'message'=>'Versi event tidak lebih baru dari versi central.'];
                continue;
            }
            SyncQueue::create([
                'sync_event_uuid'=>$event['event_uuid'],'village_uuid'=>$data['village_uuid'],'device_uuid'=>$data['device_uuid'],
                'table_name'=>$event['table'],'record_uuid'=>$event['record_uuid'],'operation'=>$event['operation']==='CREATE'?'INSERT':$event['operation'],
                'record_version'=>$event['version'],'payload'=>$event['payload']??null,'created_at'=>now(),'status'=>'SYNCED','synced_at'=>now()
            ]);
            $synced[]=$event['event_uuid'];
        }
        $device->forceFill(['last_seen_at'=>now(),'last_sync_at'=>now()])->save();
        return response()->json(['success'=>true,'synced_event_uuids'=>$synced,'conflicts'=>$conflicts,'server_time'=>now()->toIso8601String()]);
    }

    public function pull(Request $request)
    {
        $data=$request->validate(['village_uuid'=>'required|uuid','device_uuid'=>'required|uuid','cursor'=>'nullable|string|max:190']);
        $device=Device::where('uuid',$data['device_uuid'])->where('village_uuid',$data['village_uuid'])->first();
        abort_unless($device && $device->status==='active',401);
        $after=$data['cursor'] ? base64_decode($data['cursor'],true) : '1970-01-01T00:00:00.000000Z';
        $query=SyncQueue::where('village_uuid',$data['village_uuid'])->where('status','SYNCED')->where('created_at','>',date('Y-m-d H:i:s',strtotime($after)))->orderBy('created_at')->orderBy('id')->limit(100);
        $events=$query->get()->map(fn($e)=>['event_uuid'=>$e->sync_event_uuid,'table'=>$e->table_name,'record_uuid'=>$e->record_uuid,'operation'=>$e->operation,'version'=>(int)$e->record_version,'payload'=>$e->payload,'created_at'=>$e->created_at?->toIso8601String()])->values();
        $last=$events->last();$next=$last?base64_encode($last['created_at']):$data['cursor'];
        return response()->json(['success'=>true,'changes'=>$events,'next_cursor'=>$next,'server_time'=>now()->toIso8601String()]);
    }
}