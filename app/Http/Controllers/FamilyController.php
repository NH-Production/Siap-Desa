<?php

namespace App\Http\Controllers;

use App\Models\Citizen;
use App\Models\Family;
use App\Models\FamilyMember;
use App\Models\Village;
use App\Services\Audit\AuditService;
use Illuminate\Http\Request;

class FamilyController extends Controller
{
    private function rules(?Family $family=null): array {
        return [
            'no_kk'=>['required','digits:16','unique:families,no_kk'.($family?','.$family->id:'')],
            'head_name'=>'required|string|max:150','head_nik'=>'nullable|digits:16',
            'address'=>'nullable|string','rt'=>'nullable|string|max:5','rw'=>'nullable|string|max:5',
            'postal_code'=>'nullable|string|max:10','issue_date'=>'nullable|date','notes'=>'nullable|string'
        ];
    }

    public function index(Request $request) {
        $q=Family::with('members.citizen');
        if($s=$request->input('search')) $q->where(fn($x)=>$x->where('no_kk','like',"%$s%")->orWhere('head_name','like',"%$s%")->orWhere('head_nik','like',"%$s%"));
        $families=$q->latest()->paginate(20)->withQueryString();
        return view('families.index',compact('families'));
    }

    public function create(){return view('families.create');}

    public function store(Request $request){
        $data=$request->validate($this->rules());
        $data['village_id']=Village::firstOrFail()->uuid;
        $family=Family::create($data);
        $this->linkHead($family,$data['head_nik']??null);
        AuditService::log('CREATE','families',$family->uuid,null,$family->toArray());
        return redirect()->route('families.show',$family->uuid)->with('success','Kartu Keluarga berhasil dibuat.');
    }

    public function show(string $uuid){
        $family=Family::where('uuid',$uuid)->with('members.citizen')->firstOrFail();
        $available=Citizen::where('status','!=','MENINGGAL')->orderBy('name')->get();
        return view('families.show',['family'=>$family,'availableCitizens'=>$available]);
    }

    public function edit(string $uuid){$family=Family::where('uuid',$uuid)->firstOrFail();return view('families.edit',compact('family'));}

    public function update(Request $request,string $uuid){
        $family=Family::where('uuid',$uuid)->firstOrFail();$old=$family->toArray();
        $data=$request->validate($this->rules($family));$family->update($data);$this->linkHead($family,$data['head_nik']??null);
        AuditService::log('UPDATE','families',$family->uuid,$old,$family->toArray());
        return redirect()->route('families.show',$family->uuid)->with('success','Kartu Keluarga diperbarui.');
    }

    public function destroy(string $uuid){
        $family=Family::where('uuid',$uuid)->firstOrFail();$old=$family->toArray();$family->delete();
        AuditService::log('DELETE','families',$family->uuid,$old);
        return redirect()->route('families.index')->with('success','Kartu Keluarga dihapus.');
    }

    public function addMember(Request $request,string $uuid){
        $family=Family::where('uuid',$uuid)->firstOrFail();
        $data=$request->validate(['citizen_id'=>'required|exists:citizens,id','relation_status'=>'required|string|max:30']);
        $member=FamilyMember::updateOrCreate(['citizen_id'=>$data['citizen_id']],['family_id'=>$family->id,'relation_status'=>$data['relation_status']]);
        $citizen=Citizen::find($data['citizen_id']);$citizen?->update(['no_kk'=>$family->no_kk]);
        AuditService::log('UPDATE','family_members',$member->uuid,null,$member->toArray());
        return back()->with('success','Anggota keluarga berhasil ditambahkan.');
    }

    public function removeMember(string $memberUuid){
        $member=FamilyMember::where('uuid',$memberUuid)->firstOrFail();$member->delete();
        return back()->with('success','Anggota keluarga dikeluarkan.');
    }

    private function linkHead(Family $family,?string $nik): void {
        if(!$nik)return;
        $citizen=Citizen::where('nik',$nik)->first();if(!$citizen)return;
        FamilyMember::updateOrCreate(['citizen_id'=>$citizen->id],['family_id'=>$family->id,'relation_status'=>'KEPALA_KELUARGA']);
        $citizen->update(['no_kk'=>$family->no_kk]);
    }
}
