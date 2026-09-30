<?php
namespace App\Services\Sync;

use App\Models\SyncCheckpoint;
use App\Models\SyncConflict;
use App\Models\SyncQueue;
use App\Services\DeviceIdentityService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class SyncEngine
{
    public function __construct(private DeviceIdentityService $identity) {}

    private function base(): string { return rtrim(config('siapdesa.sync.central_api_url', ''), '/'); }

    private function headers(): array {
        return [
            'Authorization' => 'Bearer '.env('CENTRAL_API_KEY', ''),
            'X-SIAP-Protocol' => (string) config('siapdesa.sync_protocol_version', 1),
            'Accept' => 'application/json',
        ];
    }

    private function checkpoint(): SyncCheckpoint {
        return SyncCheckpoint::firstOrCreate(
            ['village_uuid'=>config('siapdesa.village_uuid'),'device_uuid'=>$this->identity->getDeviceUuid()],
            ['updated_at'=>now()]
        );
    }

    public function checkCentral(): bool {
        if ($this->base() === '') return false;
        try { return Http::timeout(config('siapdesa.sync.timeout_seconds',20))->withHeaders($this->headers())->get($this->base().'/health')->successful(); }
        catch (\Throwable) { return false; }
    }

    public function pushChanges(): array {
        if (!config('siapdesa.sync.enabled',true) || $this->base()==='') return ['status'=>'DISABLED','pushed'=>0];
        $items=SyncQueue::whereIn('status',['PENDING','FAILED'])->where('attempts','<',config('siapdesa.sync.max_attempts',8))->orderBy('id')->limit(config('siapdesa.sync.batch_size',100))->get();
        if($items->isEmpty()) return ['status'=>'IDLE','pushed'=>0];
        $ids=$items->pluck('id');
        SyncQueue::whereIn('id',$ids)->update(['status'=>'PROCESSING','attempts'=>DB::raw('attempts+1'),'last_attempt_at'=>now()]);
        try {
            $res=Http::timeout(config('siapdesa.sync.timeout_seconds',20))->withHeaders($this->headers())->post($this->base().'/sync/push',[
                'protocol_version'=>config('siapdesa.sync_protocol_version',1),
                'village_uuid'=>config('siapdesa.village_uuid'),
                'device_uuid'=>$this->identity->getDeviceUuid(),
                'events'=>$items->map(fn($i)=>[
                    'event_uuid'=>$i->sync_event_uuid,'table'=>$i->table_name,'record_uuid'=>$i->record_uuid,
                    'operation'=>$i->operation==='INSERT'?'CREATE':$i->operation,'version'=>(int)$i->record_version,
                    'payload'=>$i->payload,'created_at'=>$i->created_at?->toIso8601String()
                ])->values()->all()
            ]);
            if(!$res->successful()) throw new \RuntimeException('HTTP '.$res->status());
            $body=$res->json(); $synced=$body['synced_event_uuids']??[]; $conflicts=$body['conflicts']??[];
            if($synced) SyncQueue::whereIn('sync_event_uuid',$synced)->update(['status'=>'SYNCED','synced_at'=>now()]);
            foreach($conflicts as $c){
                SyncQueue::where('sync_event_uuid',$c['event_uuid']??'')->update(['status'=>'CONFLICT','last_error'=>$c['message']??'Conflict']);
                SyncConflict::create(['uuid'=>Str::uuid(),'village_uuid'=>config('siapdesa.village_uuid'),'device_uuid'=>$this->identity->getDeviceUuid(),'table_name'=>$c['table']??'unknown','record_uuid'=>$c['record_uuid']??'','local_version'=>$c['local_version']??null,'remote_version'=>$c['remote_version']??null,'local_payload'=>$c['local_payload']??null,'remote_payload'=>$c['remote_payload']??null]);
            }
            $this->checkpoint()->update(['last_push_at'=>now(),'updated_at'=>now()]);
            return ['status'=>'SUCCESS','pushed'=>count($synced),'conflicts'=>count($conflicts)];
        } catch(\Throwable $e) {
            SyncQueue::whereIn('id',$ids)->update(['status'=>'FAILED','last_error'=>mb_substr($e->getMessage(),0,1000)]);
            return ['status'=>'FAILED','pushed'=>0,'error'=>$e->getMessage()];
        }
    }

    public function pullChanges(): array {
        if(!config('siapdesa.sync.enabled',true) || $this->base()==='') return ['status'=>'DISABLED','pulled'=>0];
        $cp=$this->checkpoint();
        try {
            $res=Http::timeout(config('siapdesa.sync.timeout_seconds',20))->withHeaders($this->headers())->get($this->base().'/sync/pull',[
                'village_uuid'=>config('siapdesa.village_uuid'),'device_uuid'=>$this->identity->getDeviceUuid(),'cursor'=>$cp->remote_cursor
            ]);
            if(!$res->successful()) throw new \RuntimeException('HTTP '.$res->status());
            $body=$res->json(); $applied=0;
            foreach($body['changes']??[] as $change){$this->applyRemote($change);$applied++;}
            $cp->update(['remote_cursor'=>$body['next_cursor']??$cp->remote_cursor,'last_pull_at'=>now(),'updated_at'=>now()]);
            return ['status'=>'SUCCESS','pulled'=>$applied];
        } catch(\Throwable $e) { return ['status'=>'FAILED','pulled'=>0,'error'=>$e->getMessage()]; }
    }

    private function applyRemote(array $change): void {
        $class=config('siapdesa.sync_models.'.$change['table'],null);
        if(!$class || empty($change['record_uuid'])) return;
        DB::transaction(function() use($class,$change){
            $m=$class::where('uuid',$change['record_uuid'])->first();
            if(($change['operation']??'UPDATE')==='DELETE'){if($m)$m->delete();return;}
            $payload=$change['payload']??[];
            if($m)$m->forceFill($payload)->saveQuietly();
            else $class::withoutEvents(fn()=> $class::create($payload));
        });
    }
}