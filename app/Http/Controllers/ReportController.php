<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Attendance;
use App\Models\Citizen;
use App\Models\FinanceTransaction;
use App\Models\Village;
use App\Services\Audit\AuditService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index()
    {
        return view('reports.index');
    }

    public function citizensReport(Request $request)
    {
        $village = Village::first();
        $citizens = Citizen::all();
        $totalByGender = Citizen::groupBy('gender')->selectRaw('gender, count(*) as total')->pluck('total', 'gender');
        $totalByReligion = Citizen::groupBy('religion')->selectRaw('religion, count(*) as total')->pluck('total', 'religion');

        AuditService::log('REPORT', 'citizens', null, null, ['report' => 'Kependudukan']);

        return view('reports.citizens', compact('village', 'citizens', 'totalByGender', 'totalByReligion'));
    }

    public function financeReport(Request $request)
    {
        $village = Village::first();
        $transactions = FinanceTransaction::with('account')->latest('transaction_date')->get();
        $income = FinanceTransaction::where('type', 'PENERIMAAN')->where('status', 'POSTED')->sum('amount');
        $expense = FinanceTransaction::where('type', 'PENGELUARAN')->where('status', 'POSTED')->sum('amount');

        AuditService::log('REPORT', 'finance', null, null, ['report' => 'Keuangan']);

        return view('reports.finance', compact('village', 'transactions', 'income', 'expense'));
    }

    public function attendanceReport(Request $request)
    {
        $village = Village::first();
        $month = $request->input('month', date('Y-m'));
        $attendances = Attendance::with('employee')->where('date', 'like', "{$month}%")->latest('date')->get();

        AuditService::log('REPORT', 'attendance', null, null, ['month' => $month]);

        return view('reports.attendance', compact('village', 'attendances', 'month'));
    }

    public function aidReport(Request $request)
    {
        $village=Village::first();
        $year=(int)$request->input('year',date('Y'));
        $programs=\App\Models\AidProgram::withCount('recipients')->with('recipients.citizen')->where('year',$year)->get();
        AuditService::log('REPORT','aid',null,null,['year'=>$year]);
        return view('reports.aid',compact('village','programs','year'));
    }

    public function lettersReport(Request $request)
    {
        $village=Village::first();
        $from=$request->input('from',date('Y-m-01')); $to=$request->input('to',date('Y-m-d'));
        $letters=\App\Models\Letter::with('letterType')->whereBetween('created_at',[$from.' 00:00:00',$to.' 23:59:59'])->latest()->get();
        AuditService::log('REPORT','letters',null,null,['from'=>$from,'to'=>$to]);
        return view('reports.letters',compact('village','letters','from','to'));
    }

    public function pdf(string $type, Request $request)
    {
        $viewMap=['citizens'=>'reports.citizens','finance'=>'reports.finance','attendance'=>'reports.attendance','assets'=>'reports.assets','aid'=>'reports.aid','letters'=>'reports.letters'];
        abort_unless(isset($viewMap[$type]),404);
        $response=$this->{$type.'Report'}($request);
        $html=$response->render();
        $pdf=\Dompdf\Dompdf::class;
        $dompdf=new $pdf(); $dompdf->loadHtml($html); $dompdf->setPaper('A4','landscape'); $dompdf->render();
        return response($dompdf->output(),200,['Content-Type'=>'application/pdf','Content-Disposition'=>'inline; filename="SIAP-DESA-'.$type.'-'.date('YmdHis').'.pdf"']);
    }

    public function assetsReport(Request $request)
    {
        $village = Village::first();
        $assets = Asset::with('category')->where('status', 'AKTIF')->get();
        $totalValue = $assets->sum('acquisition_cost');

        AuditService::log('REPORT', 'assets', null, null, ['report' => 'Aset/Inventaris']);

        return view('reports.assets', compact('village', 'assets', 'totalValue'));
    }
}
