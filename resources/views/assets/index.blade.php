@extends('layouts.app')

@section('title', 'Aset & Inventaris Desa')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1">Aset & Inventaris Pemerintahan Desa</h4>
        <p class="text-muted small mb-0">Pencatatan barang milik desa, tanah, bangunan, peralatan, dan mutasi kondisi.</p>
    </div>
    <a href="{{ route('assets.create') }}" class="btn btn-primary btn-sm">
        <i class="fa-solid fa-plus me-1"></i> Daftarkan Aset Baru
    </a>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light small">
                <tr>
                    <th>Kode Aset</th>
                    <th>Nama Barang / Aset</th>
                    <th>Kategori</th>
                    <th>Nilai Perolehan</th>
                    <th>Kondisi</th>
                    <th>Lokasi</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody class="small">
                @forelse($assets as $a)
                <tr>
                    <td><code>{{ $a->asset_code }}</code></td>
                    <td class="fw-bold">{{ $a->name }}</td>
                    <td>{{ $a->category->name ?? '-' }}</td>
                    <td>Rp {{ number_format($a->acquisition_cost, 0, ',', '.') }}</td>
                    <td>
                        <span class="badge bg-{{ $a->condition == 'BAIK' ? 'success' : ($a->condition == 'RUSAK_RINGAN' ? 'warning' : 'danger') }}">
                            {{ $a->condition }}
                        </span>
                    </td>
                    <td>{{ $a->location ?? '-' }}</td>
                    <td><span class="badge bg-light text-dark border">{{ $a->status }}</span></td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center text-muted py-4">Belum ada data aset desa terdaftar.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
