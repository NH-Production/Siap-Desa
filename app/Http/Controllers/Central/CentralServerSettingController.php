<?php

namespace App\Http\Controllers\Central;

use App\Http\Controllers\Controller;
use App\Models\CentralServerSetting;
use App\Services\Supabase\SupabaseService;
use Illuminate\Http\Request;

class CentralServerSettingController extends Controller
{
    public function index()
    {
        $supabaseUrl = CentralServerSetting::get('supabase_url', 'https://siapdesa.supabase.co');
        $supabaseKey = CentralServerSetting::get('supabase_key', 'sb_publishable_UeCe6pqUTtRhBgLjpT1djA_anoXjXve');
        $centralApiKey = CentralServerSetting::get('central_api_key', 'sb_publishable_UeCe6pqUTtRhBgLjpT1djA_anoXjXve');
        $syncInterval = CentralServerSetting::get('sync_interval_seconds', '30');
        $realtimeSync = CentralServerSetting::get('realtime_sync_enabled', '1');
        $githubRepo = CentralServerSetting::get('github_repo', 'NH-Production/Siap-Desa');

        return view('central.settings', compact('supabaseUrl', 'supabaseKey', 'centralApiKey', 'syncInterval', 'realtimeSync', 'githubRepo'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'supabase_url' => 'required|string',
            'supabase_key' => 'required|string',
            'sync_interval_seconds' => 'required|integer|min:5|max:3600',
            'github_repo' => 'required|string',
        ]);

        CentralServerSetting::set('supabase_url', $request->supabase_url, 'Supabase Cloud REST URL');
        CentralServerSetting::set('supabase_key', $request->supabase_key, 'Supabase API Publishable Key');
        CentralServerSetting::set('central_api_key', $request->supabase_key, 'Central API Access Key');
        CentralServerSetting::set('sync_interval_seconds', $request->sync_interval_seconds, 'Global Client Sync Interval (detik)');
        CentralServerSetting::set('realtime_sync_enabled', $request->boolean('realtime_sync_enabled') ? '1' : '0', 'Aktifkan Realtime Push Sync');
        CentralServerSetting::set('github_repo', $request->github_repo, 'GitHub Repository Identifier');

        return back()->with('success', 'Pengaturan Server Central & Sinkronisasi Supabase Cloud berhasil diperbarui!');
    }

    public function testSupabase(SupabaseService $supabase)
    {
        $result = $supabase->testConnection();
        return response()->json($result);
    }
}
