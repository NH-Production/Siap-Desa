<?php

namespace App\Http\Controllers\Central;

use App\Http\Controllers\Controller;
use App\Models\CentralDevice;
use App\Models\CentralLicense;
use App\Models\CentralRelease;
use App\Models\CentralSyncLog;
use App\Models\CentralVillage;
use Illuminate\Http\Request;

class CentralDashboardController extends Controller
{
    public function index()
    {
        $totalVillages = CentralVillage::count();
        $activeLicenses = CentralLicense::where('status', 'ACTIVE')->count();
        $totalDevices = CentralDevice::count();
        $todaySyncs = CentralSyncLog::whereDate('created_at', today())->count();
        $latestRelease = CentralRelease::where('is_published', true)->latest()->first();

        $recentSyncs = CentralSyncLog::latest()->take(10)->get();
        $villages = CentralVillage::with('activeLicense')->latest()->take(5)->get();

        return view('central.dashboard', compact(
            'totalVillages', 'activeLicenses', 'totalDevices', 'todaySyncs',
            'latestRelease', 'recentSyncs', 'villages'
        ));
    }
}
