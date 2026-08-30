<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\AssetCategory;
use App\Models\AssetMutation;
use App\Models\Village;
use App\Services\Audit\AuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AssetController extends Controller
{
    public function index()
    {
        $assets = Asset::with('category')->latest()->paginate(15);
        $categories = AssetCategory::all();
        $totalCost = Asset::where('status', 'AKTIF')->sum('acquisition_cost');

        return view('assets.index', compact('assets', 'categories', 'totalCost'));
    }

    public function create()
    {
        $categories = AssetCategory::all();
        return view('assets.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'category_id' => 'required|exists:asset_categories,id',
            'acquisition_date' => 'nullable|date',
            'acquisition_source' => 'nullable|string|max:80',
            'acquisition_cost' => 'required|numeric|min:0',
            'condition' => 'required|in:BAIK,RUSAK_RINGAN,RUSAK_BERAT',
            'location' => 'nullable|string|max:150',
            'custodian' => 'nullable|string|max:100',
        ]);

        $village = Village::first();
        $assetCount = Asset::count() + 1;
        $assetCode = 'AST-' . date('Y') . '-' . str_pad($assetCount, 4, '0', STR_PAD_LEFT);

        $validated['village_id'] = $village->uuid ?? Str::uuid()->toString();
        $validated['asset_code'] = $assetCode;
        $validated['status'] = 'AKTIF';

        $asset = Asset::create($validated);

        AuditService::log('CREATE', 'assets', $asset->uuid, null, $asset->toArray());

        return redirect()->route('assets.index')->with('success', "Aset {$assetCode} berhasil didaftarkan.");
    }

    public function show($uuid)
    {
        $asset = Asset::where('uuid', $uuid)->with('category', 'mutations')->firstOrFail();
        return view('assets.show', compact('asset'));
    }

    public function storeMutation(Request $request, $uuid)
    {
        $asset = Asset::where('uuid', $uuid)->firstOrFail();

        $validated = $request->validate([
            'mutation_type' => 'required|string',
            'date' => 'required|date',
            'reason' => 'required|string',
            'new_value' => 'required|string',
        ]);

        $prevVal = match($validated['mutation_type']) {
            'PINDAH_LOKASI' => $asset->location,
            'PERUBAHAN_KONDISI' => $asset->condition,
            'PENGHAPUSAN' => $asset->status,
            default => '',
        };

        AssetMutation::create([
            'asset_id' => $asset->id,
            'mutation_type' => $validated['mutation_type'],
            'previous_value' => $prevVal,
            'new_value' => $validated['new_value'],
            'date' => $validated['date'],
            'reason' => $validated['reason'],
        ]);

        // Update asset state
        if ($validated['mutation_type'] === 'PINDAH_LOKASI') {
            $asset->update(['location' => $validated['new_value']]);
        } elseif ($validated['mutation_type'] === 'PERUBAHAN_KONDISI') {
            $asset->update(['condition' => $validated['new_value']]);
        } elseif ($validated['mutation_type'] === 'PENGHAPUSAN') {
            $asset->update(['status' => 'DIHAPUSKAN']);
        }

        AuditService::log('UPDATE', 'assets', $asset->uuid, null, ['mutation' => $validated]);

        return back()->with('success', 'Mutasi / perubahan status aset berhasil dicatat.');
    }

    public function destroy($uuid)
    {
        $asset = Asset::where('uuid', $uuid)->firstOrFail();
        $asset->delete();

        AuditService::log('DELETE', 'assets', $asset->uuid);

        return redirect()->route('assets.index')->with('success', 'Data aset telah dihapus.');
    }
}
