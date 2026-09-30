<?php

namespace App\Http\Controllers;

use App\Models\Citizen;
use App\Models\Employee;
use App\Models\EmployeePosition;
use App\Models\Village;
use App\Services\Audit\AuditService;
use App\Services\QrCode\QrService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        $q=Employee::with('citizen');
        if($s=$request->input('search')) $q->where(fn($x)=>$x->where('name','like',"%$s%")->orWhere('nip','like',"%$s%")->orWhere('position','like',"%$s%"));
        if($request->has('active')) $q->where('is_active',(bool)$request->boolean('active'));
        $employees=$q->latest()->paginate(15)->withQueryString();
        return view('employees.index',compact('employees'));
    }

    public function create(){ $citizens=Citizen::orderBy('name')->get(); return view('employees.create',compact('citizens')); }

    public function store(Request $request)
    {
        $data=$request->validate([
            'name'=>'required|string|max:150','citizen_id'=>'nullable|exists:citizens,id',
            'nip'=>'nullable|string|max:30','position'=>'required|string|max:100',
            'department'=>'nullable|string|max:100','employment_status'=>'required|string|max:40','join_date'=>'nullable|date'
        ]);
        $data['village_id']=Village::firstOrFail()->uuid;
        $data['qr_token']='QR-EMP-'.strtoupper(Str::random(24));
        $data['is_active']=true;
        $employee=Employee::create($data);
        EmployeePosition::create(['employee_id'=>$employee->id,'position'=>$employee->position,'department'=>$employee->department,'start_date'=>$employee->join_date,'is_current'=>true]);
        AuditService::log('CREATE','employees',$employee->uuid,null,$employee->toArray());
        return redirect()->route('employees.show',$employee->uuid)->with('success','Aparat/pegawai desa berhasil ditambahkan.');
    }

    public function show(string $uuid){$employee=Employee::where('uuid',$uuid)->with('citizen','positions','attendances')->firstOrFail();return view('employees.show',compact('employee'));}

    public function qrCard(string $uuid){$employee=Employee::where('uuid',$uuid)->firstOrFail();$village=Village::first();$qrDataUri=(new QrService())->generateDataUri($employee->qr_token,250);return view('employees.qr-card',compact('employee','village','qrDataUri'));}

    public function rotateQr(string $uuid){$employee=Employee::where('uuid',$uuid)->firstOrFail();$old=$employee->qr_token;$employee->update(['qr_token'=>'QR-EMP-'.strtoupper(Str::random(24))]);AuditService::log('UPDATE','employees',$employee->uuid,['qr_token'=>$old],['qr_token'=>$employee->qr_token]);return back()->with('success','Token QR berhasil dirotasi.');}

    public function update(Request $request,string $uuid){
        $employee=Employee::where('uuid',$uuid)->firstOrFail();$old=$employee->toArray();
        $data=$request->validate(['name'=>'required|string|max:150','citizen_id'=>'nullable|exists:citizens,id','nip'=>'nullable|string|max:30','position'=>'required|string|max:100','department'=>'nullable|string|max:100','employment_status'=>'required|string|max:40','join_date'=>'nullable|date','is_active'=>'boolean']);
        $employee->update($data);
        EmployeePosition::where('employee_id',$employee->id)->where('is_current',true)->update(['is_current'=>false,'end_date'=>now()->toDateString()]);
        EmployeePosition::create(['employee_id'=>$employee->id,'position'=>$employee->position,'department'=>$employee->department,'start_date'=>$employee->join_date,'is_current'=>true]);
        AuditService::log('UPDATE','employees',$employee->uuid,$old,$employee->toArray());
        return redirect()->route('employees.show',$employee->uuid)->with('success','Data aparat desa diperbarui.');
    }

    public function destroy(string $uuid){$employee=Employee::where('uuid',$uuid)->firstOrFail();$employee->update(['is_active'=>false]);AuditService::log('UPDATE','employees',$employee->uuid,null,['action'=>'DEACTIVATE']);return redirect()->route('employees.index')->with('success','Pegawai dinonaktifkan.');}
}
