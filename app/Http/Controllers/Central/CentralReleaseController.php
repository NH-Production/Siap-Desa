<?php

namespace App\Http\Controllers\Central;

use App\Http\Controllers\Controller;
use App\Models\CentralRelease;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class CentralReleaseController extends Controller
{
    public function index()
    {
        $releases = CentralRelease::latest('release_date')->paginate(15);
        return view('central.releases.index', compact('releases'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'version' => 'required|string|unique:central_releases,version',
            'schema_version' => 'required|integer',
            'title' => 'required|string|max:150',
            'changelog' => 'required|string',
            'patch_file' => 'nullable|file|mimes:zip,pkg|max:102400',
        ]);

        $filePath = null;
        $fileName = null;
        $fileSize = 0;
        $checksum = null;

        if ($request->hasFile('patch_file')) {
            $file = $request->file('patch_file');
            $fileName = "SIAP_Desa_Patch_v{$request->version}.zip";
            $destDir = public_path('downloads/patches');
            File::ensureDirectoryExists($destDir);
            $destPath = $destDir . '/' . $fileName;
            $file->move($destDir, $fileName);

            $filePath = 'downloads/patches/' . $fileName;
            $fileSize = File::size($destPath);
            $checksum = hash_file('sha256', $destPath);
        }

        CentralRelease::create([
            'version' => $request->version,
            'schema_version' => $request->schema_version,
            'title' => $request->title,
            'changelog' => $request->changelog,
            'file_path' => $filePath,
            'file_name' => $fileName,
            'file_size' => $fileSize,
            'checksum_sha256' => $checksum,
            'is_mandatory' => $request->boolean('is_mandatory'),
            'is_published' => true,
            'release_date' => now(),
        ]);

        return back()->with('success', "Versi rilis v{$request->version} berhasil dipublikasikan untuk seluruh klien desa!");
    }
}
