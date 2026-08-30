@extends('layouts.app')

@section('title', 'Aparat & Pegawai Desa')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1">Aparat & Pegawai Pemerintah Desa</h4>
        <p class="text-muted small mb-0">Kelola struktur perangkat desa, jabatan, dan kartu absensi QR Code.</p>
    </div>
    <a href="{{ route('employees.create') }}" class="btn btn-primary btn-sm">
        <i class="fa-solid fa-user-plus me-1"></i> Tambah Pegawai Baru
    </a>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light small">
                <tr>
                    <th>Nama Pegawai</th>
                    <th>NIP / NIPD</th>
                    <th>Jabatan</th>
                    <th>Status</th>
                    <th>QR Token</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody class="small">
                @forelse($employees as $e)
                <tr>
                    <td>
                        <div class="fw-bold">{{ $e->name }}</div>
                        <small class="text-muted">{{ $e->department ?? 'Pemerintah Desa' }}</small>
                    </td>
                    <td><code>{{ $e->nip ?? '-' }}</code></td>
                    <td><span class="badge bg-primary">{{ $e->position }}</span></td>
                    <td>
                        <span class="badge bg-{{ $e->is_active ? 'success' : 'danger' }}">
                            {{ $e->is_active ? 'Aktif' : 'Non-Aktif' }}
                        </span>
                    </td>
                    <td><code>{{ $e->qr_token }}</code></td>
                    <td class="text-end">
                        <a href="{{ route('employees.qr-card', $e->uuid) }}" class="btn btn-sm btn-outline-success" target="_blank">
                            <i class="fa-solid fa-qrcode me-1"></i> Cetak Kartu QR
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center text-muted py-4">Belum ada data pegawai terdaftar.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
