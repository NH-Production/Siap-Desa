@extends('layouts.central')

@section('title', 'Manajemen Desa / Tenants')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1">Manajemen Desa (Multi-Tenant Hub)</h4>
        <p class="text-muted small mb-0">Daftar instansi pemerintah desa yang terdaftar pada sistem cloud.</p>
    </div>
    <a href="{{ route('central.villages.create') }}" class="btn btn-primary btn-sm">
        <i class="fa-solid fa-plus me-1"></i> Tambah Desa Baru
    </a>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light small">
                <tr>
                    <th>Kode Kemendagri</th>
                    <th>Nama Desa</th>
                    <th>Kecamatan / Kabupaten</th>
                    <th>Kepala Desa</th>
                    <th>Kontak Pemdes</th>
                    <th>Kode Lisensi Aktif</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody class="small">
                @forelse($villages as $v)
                <tr>
                    <td><code>{{ $v->code }}</code></td>
                    <td class="fw-bold">{{ $v->name }}</td>
                    <td>Kec. {{ $v->district }}, {{ $v->regency }}</td>
                    <td>{{ $v->head_name ?? '-' }}</td>
                    <td>{{ $v->phone ?? '-' }}</td>
                    <td>
                        @if($v->activeLicense)
                            <code class="text-primary fw-bold">{{ $v->activeLicense->license_key }}</code>
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>
                    <td>
                        <span class="badge bg-{{ $v->status == 'ACTIVE' ? 'success' : 'warning' }}">{{ $v->status }}</span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center text-muted py-4">Belum ada data desa terdaftar.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
