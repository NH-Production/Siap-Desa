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
        $data = $request->validate([
            'version' => ['required','regex:/^\d+\.\d+\.\d+$/','unique:central_releases,version'],
            'minimum_version' => ['required','regex:/^\d+\.\d+\.\d+$/'],
            'schema_version' => ['required','integer','min:1'],
            'sync_protocol' => ['required','integer','min:1'],
            'title' => ['required','string','max:150'],
            'changelog' => ['required','string'],
            'patch_file' => ['required','file','mimes:zip','max:512000'],
        ]);

        $file = $request->file('patch_file');
        $fileName = 'SIAP-DESA-Patch-'.$data['version'].'.zip';
        $destDir = public_path('downloads/patches');
        File::ensureDirectoryExists($destDir);
        $destPath = $destDir.'/'.$fileName;
        $file->move($destDir, $fileName);

        $release = CentralRelease::create([
            'uuid' => (string) Str::uuid(),
            'version' => $data['version'],
            'minimum_version' => $data['minimum_version'],
            'schema_version' => $data['schema_version'],
            'sync_protocol' => $data['sync_protocol'],
            'title' => $data['title'],
            'changelog' => $data['changelog'],
            'file_path' => 'downloads/patches/'.$fileName,
            'file_name' => $fileName,
            'file_size' => File::size($destPath),
            'checksum_sha256' => hash_file('sha256', $destPath),
            'is_mandatory' => $request->boolean('is_mandatory'),
            'is_published' => $request->boolean('is_published', true),
            'release_date' => now(),
        ]);

        return back()->with('success', 'Rilis '.$release->version.' berhasil dipublikasikan.');
    }
}
