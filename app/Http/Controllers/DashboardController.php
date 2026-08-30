<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Citizen;
use App\Models\Employee;
use App\Models\Family;
use App\Models\FinanceTransaction;
use App\Models\Letter;
use App\Models\SyncConflict;
use App\Models\SyncQueue;
use App\Models\Village;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today()->toDateString();
        
        $totalCitizens = Citizen::count();
        $totalFamilies = Family::count();
        $totalEmployees = Employee::where('is_active', true)->count();
        $todayAttendance = Attendance::where('date', $today)->where('status', 'HADIR')->count();
        
        $totalLetters = Letter::count();
        $pendingLetters = Letter::where('status', 'DRAFT')->orWhere('status', 'REVIEW')->count();

        // Finance balance calculation
        $totalIncome = FinanceTransaction::where('type', 'PENERIMAAN')->where('status', 'POSTED')->sum('amount');
        $totalExpense = FinanceTransaction::where('type', 'PENGELUARAN')->where('status', 'POSTED')->sum('amount');
        $cashBalance = $totalIncome - $totalExpense;

        // Sync & health indicators
        $pendingPushCount = SyncQueue::where('status', 'PENDING')->count();
        $conflictCount = SyncConflict::whereNull('resolved_at')->count();

        $recentLetters = Letter::with('letterType')->latest()->limit(5)->get();
        $recentTransactions = FinanceTransaction::with('account')->latest()->limit(5)->get();
        $recentAttendances = Attendance::with('employee')->where('date', $today)->latest()->limit(5)->get();

        $village = Village::first();

        return view('dashboard.index', compact(
            'totalCitizens',
            'totalFamilies',
            'totalEmployees',
            'todayAttendance',
            'totalLetters',
            'pendingLetters',
            'totalIncome',
            'totalExpense',
            'cashBalance',
            'pendingPushCount',
            'conflictCount',
            'recentLetters',
            'recentTransactions',
            'recentAttendances',
            'village'
        ));
    }
}
