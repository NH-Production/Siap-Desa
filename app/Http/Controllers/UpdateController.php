<?php

namespace App\Http\Controllers;

use App\Models\SystemVersion;
use App\Models\UpdateLog;
use App\Services\Audit\AuditService;
use App\Services\Backup\BackupService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use ZipArchive;

class UpdateController extends Controller
{
    public function index()
    {
        $currentVersion = SystemVersion::orderBy('id', 'desc')->first();
        $updateLogs = UpdateLog::latest()->get();
        $githubRepo = config('siap.github_repo', 'NH-Production/Siap-Desa');

        return view('system.updates', compact('currentVersion', 'updateLogs', 'githubRepo'));
    }

    public function checkOnline(Request $request)
    {
        $currentVer = SystemVersion::orderBy('id', 'desc')->first();
        $ver = $currentVer->app_version ?? '1.0.0';
        $centralUrl = rtrim(config('siap.central_api_url', 'https://api.siapdesa.id/api/v1'), '/');
        $githubRepo = config('siap.github_repo', 'NH-Production/Siap-Desa');

        // Check 1: Central Server API
        try {
            $res = Http::timeout(5)->get($centralUrl . '/updates/check', ['version' => $ver]);
            if ($res->successful()) {
                return response()->json($res->json());
            }
        } catch (\Throwable $e) { }

        // Check 2: GitHub Releases API Fallback
        try {
            $ghRes = Http::timeout(5)
                ->withHeaders(['User-Agent' => 'SIAP-Desa-App'])
                ->get("https://api.github.com/repos/{$githubRepo}/releases/latest");

            if ($ghRes->successful()) {
                $release = $ghRes->json();
                $tagName = ltrim($release['tag_name'] ?? '1.0.0', 'v');
                $hasUpdate = version_compare($tagName, $ver, '>');

                return response()->json([
                    'update_available' => $hasUpdate,
                    'current_version' => $ver,
                    'latest_version' => $tagName,
                    'title' => $release['name'] ?? "Rilis v{$tagName}",
                    'changelog' => $release['body'] ?? 'Pembaruan resmi via GitHub Releases',
                    'release_date' => date('Y-m-d', strtotime($release['published_at'] ?? 'now')),
                    'download_url' => $release['html_url'] ?? null,
                ]);
            }
        } catch (\Throwable $e) { }

        return response()->json([
            'update_available' => false,
            'current_version' => $ver,
            'message' => 'Anda sudah menggunakan versi terbaru atau server rilis sedang offline.',
        ]);
    }

    public function applyPatch(Request $request, BackupService $backupService)
    {
        $request->validate([
            'patch_file' => 'required|file|mimes:zip,pkg|max:102400',
        ]);

        try {
            // 1. Pre-update safety backup
            $backup = $backupService->createBackup('PRE_UPDATE');

            $file = $request->file('patch_file');
            $zip = new ZipArchive();
            $res = $zip->open($file->getRealPath());

            if ($res !== true) {
                return back()->with('error', 'Gagal membuka file paket patch.');
            }

            $manifestJson = $zip->getFromName('manifest.json');
            $manifest = $manifestJson ? json_decode($manifestJson, true) : null;
            $newVersion = $manifest['version'] ?? '1.0.1';
            $newSchema = $manifest['schema_version'] ?? 1;

            $currentVer = SystemVersion::orderBy('id', 'desc')->first();

            $zip->extractTo(base_path());
            $zip->close();

            Artisan::call('migrate', ['--force' => true]);
            Artisan::call('optimize:clear');

            SystemVersion::create([
                'app_version' => $newVersion,
                'schema_version' => $newSchema,
                'sync_protocol_version' => $manifest['sync_protocol_version'] ?? 1,
                'remarks' => 'Patch update: ' . ($manifest['description'] ?? 'Pembaruan Fitur'),
            ]);

            UpdateLog::create([
                'app_version_from' => $currentVer->app_version ?? '1.0.0',
                'app_version_to' => $newVersion,
                'schema_from' => $currentVer->schema_version ?? 1,
                'schema_to' => $newSchema,
                'status' => 'SUCCESS',
                'details' => $manifest,
            ]);

            AuditService::log('UPDATE_PATCH', 'system_versions', null, null, [
                'from_version' => $currentVer->app_version ?? '1.0.0',
                'to_version' => $newVersion,
                'backup_file' => $backup->filename,
            ]);

            return back()->with('success', "Patch berhasil diterapkan! Sistem telah diperbarui ke versi v{$newVersion}.");
        } catch (\Throwable $e) {
            return back()->with('error', 'Penerapan patch gagal: ' . $e->getMessage());
        }
    }
}
