<?php

namespace App\Http\Controllers;

use App\Models\Citizen;
use App\Models\Village;
use App\Services\Audit\AuditService;
use Illuminate\Http\Request;

class CitizenController extends Controller
{
    private function rules(?Citizen $citizen = null): array
    {
        $uniqueNik = 'unique:citizens,nik' . ($citizen ? ',' . $citizen->id : '');
        return [
            'nik' => ['required','digits:16',$uniqueNik],
            'no_kk' => 'nullable|digits:16',
            'name' => 'required|string|max:150',
            'birth_place' => 'nullable|string|max:80',
            'birth_date' => 'nullable|date',
            'gender' => 'required|in:LAKI_LAKI,PEREMPUAN',
            'blood_type' => 'nullable|string|max:5',
            'religion' => 'nullable|string|max:30',
            'marital_status' => 'nullable|string|max:30',
            'occupation' => 'nullable|string|max:80',
            'education' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'rt' => 'nullable|string|max:5',
            'rw' => 'nullable|string|max:5',
            'status' => 'required|in:TETAP,PINDAH,MENINGGAL,SEMENTARA',
            'notes' => 'nullable|string',
        ];
    }

    public function index(Request $request)
    {
        $query = Citizen::query();
        foreach (['search','gender','religion','status'] as $filter) {
            if ($value = $request->input($filter)) {
                if ($filter === 'search') {
                    $query->where(fn($q) => $q->where('nik','like',"%$value%")->orWhere('name','like',"%$value%")->orWhere('no_kk','like',"%$value%")->orWhere('address','like',"%$value%"));
                } else $query->where($filter, $value);
            }
        }
        $citizens = $query->latest()->paginate(20)->withQueryString();
        return view('citizens.index', compact('citizens'));
    }

    public function create() { return view('citizens.create'); }

    public function store(Request $request)
    {
        $data = $request->validate($this->rules());
        $data['village_id'] = Village::firstOrFail()->uuid;
        $citizen = Citizen::create($data);
        AuditService::log('CREATE','citizens',$citizen->uuid,null,$citizen->toArray());
        return redirect()->route('citizens.show',$citizen->uuid)->with('success','Data penduduk berhasil ditambahkan.');
    }

    public function show(string $uuid)
    {
        $citizen = Citizen::where('uuid',$uuid)->with('familyMember.family')->firstOrFail();
        return view('citizens.show', compact('citizen'));
    }

    public function edit(string $uuid)
    {
        $citizen = Citizen::where('uuid',$uuid)->firstOrFail();
        return view('citizens.edit', compact('citizen'));
    }

    public function update(Request $request,string $uuid)
    {
        $citizen = Citizen::where('uuid',$uuid)->firstOrFail();
        $old = $citizen->toArray();
        $citizen->update($request->validate($this->rules($citizen)));
        AuditService::log('UPDATE','citizens',$citizen->uuid,$old,$citizen->toArray());
        return redirect()->route('citizens.show',$citizen->uuid)->with('success','Data penduduk diperbarui.');
    }

    public function destroy(string $uuid)
    {
        $citizen = Citizen::where('uuid',$uuid)->firstOrFail();
        $old = $citizen->toArray();
        $citizen->delete();
        AuditService::log('DELETE','citizens',$citizen->uuid,$old);
        return redirect()->route('citizens.index')->with('success','Data penduduk dihapus.');
    }

    public function exportCsv()
    {
        $citizens = Citizen::orderBy('name')->get();
        $filename = 'penduduk_' . date('Ymd_His') . '.csv';
        return response()->streamDownload(function() use ($citizens) {
            $f = fopen('php://output','w');
            fprintf($f, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($f,['NIK','No KK','Nama','JK','Tempat Lahir','Tanggal Lahir','Agama','Status Kawin','Pekerjaan','Pendidikan','Alamat','RT','RW','Status']);
            foreach($citizens as $c) fputcsv($f,[$c->nik,$c->no_kk,$c->name,$c->gender,$c->birth_place,$c->birth_date?->format('Y-m-d'),$c->religion,$c->marital_status,$c->occupation,$c->education,$c->address,$c->rt,$c->rw,$c->status]);
            fclose($f);
        },$filename,['Content-Type'=>'text/csv; charset=UTF-8']);
    }

    public function import(Request $request)
    {
        $request->validate(['file'=>'required|file|mimes:csv,txt|max:5120']);
        $handle = fopen($request->file('file')->getRealPath(),'r');
        $header = fgetcsv($handle);
        $created=$updated=$skipped=0;
        while(($row=fgetcsv($handle))!==false){
            if(count($row)<14) {$skipped++; continue;}
            $data=array_combine(['nik','no_kk','name','gender','birth_place','birth_date','religion','marital_status','occupation','education','address','rt','rw','status'],array_slice($row,0,14));
            if(!preg_match('/^\d{16}$/',$data['nik']??'')) {$skipped++; continue;}
            $citizen=Citizen::where('nik',$data['nik'])->first();
            $data['village_id']=Village::firstOrFail()->uuid;
            if($citizen){$citizen->update($data);$updated++;}else{Citizen::create($data);$created++;}
        }
        fclose($handle);
        AuditService::log('IMPORT','citizens',null,null,['created'=>$created,'updated'=>$updated,'skipped'=>$skipped]);
        return back()->with('success',"Import selesai: $created baru, $updated diperbarui, $skipped dilewati.");
    }
}
