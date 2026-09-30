<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\AttendanceCorrection;
use App\Models\Device;
use App\Models\Employee;
use App\Models\Village;
use App\Services\Audit\AuditService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{
    public function index(Request $request){
        $date=$request->input('date',Carbon::today()->toDateString());
        $attendances=Attendance::where('date',$date)->with('employee')->latest()->get();
        $employees=Employee::where('is_active',true)->orderBy('name')->get();
        return view('attendance.index',compact('attendances','employees','date'));
    }

    public function scanner(){return view('attendance.scanner');}

    public function scanSubmit(Request $request){
        $request->validate(['qr_token'=>'required|string|max:100']);
        // The scanner is intentionally local-first: no cloud request is required here.
        $employee=Employee::where('qr_token',trim($request->qr_token))->where('is_active',true)->first();
        if(!$employee)return response()->json(['success'=>false,'message'=>'Kartu QR tidak dikenali atau pegawai tidak aktif.'],404);
        $now=Carbon::now();$today=$now->toDateString();$time=$now->format('H:i:s');
        $device=Device::first();$village=Village::first();
        $existing=Attendance::where('employee_id',$employee->id)->where('date',$today)->first();

        if(!$existing){
            $status=$time>'08:00:00'?'TERLAMBAT':'HADIR';
            $att=Attendance::create(['village_id'=>$village?->uuid,'employee_id'=>$employee->id,'device_id'=>$device?->uuid,'date'=>$today,'time_in'=>$time,'status'=>$status,'notes'=>'Absensi masuk melalui QR']);
            AuditService::log('CREATE','attendance',$att->uuid,null,['employee'=>$employee->name,'type'=>'CHECK_IN']);
            return response()->json(['success'=>true,'type'=>'IN','employee_name'=>$employee->name,'position'=>$employee->position,'time'=>$time,'status'=>$status,'message'=>"Absensi masuk tercatat: {$employee->name}."]);
        }

        if($existing->time_out)return response()->json(['success'=>false,'message'=>"Absensi hari ini untuk {$employee->name} sudah lengkap."]);
        $existing->update(['time_out'=>$time]);
        AuditService::log('UPDATE','attendance',$existing->uuid,null,['employee'=>$employee->name,'type'=>'CHECK_OUT']);
        return response()->json(['success'=>true,'type'=>'OUT','employee_name'=>$employee->name,'position'=>$employee->position,'time'=>$time,'status'=>$existing->status,'message'=>"Absensi pulang tercatat: {$employee->name}."]);
    }

    public function manualStore(Request $request){
        $data=$request->validate(['employee_id'=>'required|exists:employees,id','date'=>'required|date','time_in'=>'nullable','time_out'=>'nullable','status'=>'required|in:HADIR,TERLAMBAT,PULANG_CEPAT,IZIN,SAKIT,DINAS_LUAR,ALPHA','notes'=>'nullable|string']);
        $data['village_id']=Village::first()?->uuid;$data['device_id']=Device::first()?->uuid;$data['verified_by']=Auth::id();
        $att=Attendance::updateOrCreate(['employee_id'=>$data['employee_id'],'date'=>$data['date']],$data);
        AuditService::log('UPDATE','attendance',$att->uuid,null,$att->toArray());
        return back()->with('success','Absensi manual berhasil disimpan.');
    }

    public function requestCorrection(Request $request){
        $data=$request->validate(['attendance_id'=>'required|exists:attendance,id','requested_status'=>'required|string','requested_time_in'=>'nullable','requested_time_out'=>'nullable','reason'=>'required|string']);
        $att=Attendance::findOrFail($data['attendance_id']);
        AttendanceCorrection::create(['attendance_id'=>$att->id,'employee_id'=>$att->employee_id,'requested_status'=>$data['requested_status'],'requested_time_in'=>$data['requested_time_in'],'requested_time_out'=>$data['requested_time_out'],'reason'=>$data['reason'],'status'=>'PENDING']);
        return back()->with('success','Pengajuan koreksi absensi berhasil dibuat.');
    }

    public function resolveCorrection(Request $request,string $uuid){
        $correction=AttendanceCorrection::where('uuid',$uuid)->firstOrFail();$action=$request->input('action');
        if($action==='APPROVE'){
            $correction->update(['status'=>'APPROVED','approved_by'=>Auth::id(),'approved_at'=>now()]);
            $correction->attendance->update(['status'=>$correction->requested_status,'time_in'=>$correction->requested_time_in??$correction->attendance->time_in,'time_out'=>$correction->requested_time_out??$correction->attendance->time_out,'notes'=>'Koreksi disetujui: '.$correction->reason]);
            return back()->with('success','Koreksi absensi disetujui.');
        }
        $correction->update(['status'=>'REJECTED','approved_by'=>Auth::id(),'approved_at'=>now()]);
        return back()->with('info','Koreksi absensi ditolak.');
    }
}
