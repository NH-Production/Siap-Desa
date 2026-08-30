<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CentralRelease;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class CentralUpdateApiController extends Controller
{
    public function check(Request $request)
    {
        $clientVersion = $request->query('version', '1.0.0');
        $latestRelease = CentralRelease::where('is_published', true)->latest('release_date')->first();

        if (!$latestRelease) {
            return response()->json([
                'update_available' => false,
                'message' => 'Anda sudah menggunakan versi terbaru.',
            ]);
        }

        $hasUpdate = version_compare($latestRelease->version, $clientVersion, '>');

        return response()->json([
            'update_available' => $hasUpdate,
            'current_version' => $clientVersion,
            'latest_version' => $latestRelease->version,
            'schema_version' => $latestRelease->schema_version,
            'title' => $latestRelease->title,
            'changelog' => $latestRelease->changelog,
            'is_mandatory' => $latestRelease->is_mandatory,
            'release_date' => $latestRelease->release_date->format('Y-m-d'),
            'download_url' => $hasUpdate ? route('api.updates.download', $latestRelease->version) : null,
            'checksum_sha256' => $latestRelease->checksum_sha256,
            'file_size' => $latestRelease->file_size,
        ]);
    }

    public function download($version)
    {
        $release = CentralRelease::where('version', $version)->where('is_published', true)->firstOrFail();

        if ($release->file_path && File::exists(public_path($release->file_path))) {
            return response()->download(public_path($release->file_path), $release->file_name ?? "SIAP_Desa_Patch_v{$version}.zip");
        }

        // Fallback to patches directory
        $fallback = base_path("patches/SIAP_Desa_Patch_v{$version}.zip");
        if (File::exists($fallback)) {
            return response()->download($fallback, "SIAP_Desa_Patch_v{$version}.zip");
        }

        return response()->json(['error' => 'File paket patch tidak ditemukan pada server.'], 404);
    }
}
