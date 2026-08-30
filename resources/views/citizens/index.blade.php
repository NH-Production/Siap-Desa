@extends('layouts.app')

@section('title', 'Data Penduduk')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1">Kependudukan Desa</h4>
        <p class="text-muted small mb-0">Kelola biodata warga, NIK, status kependudukan, dan riwayat keluarga.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('citizens.export.csv') }}" class="btn btn-outline-success btn-sm">
            <i class="fa-solid fa-file-excel me-1"></i> Export CSV
        </a>
        <a href="{{ route('citizens.create') }}" class="btn btn-primary btn-sm">
            <i class="fa-solid fa-user-plus me-1"></i> Tambah Penduduk
        </a>
    </div>
</div>

<!-- Search & Filter Card -->
<div class="card mb-4 p-3 bg-light border">
    <form action="{{ route('citizens.index') }}" method="GET" class="row g-2 align-items-center">
        <div class="col-md-5">
            <div class="input-group input-group-sm">
                <span class="input-group-text"><i class="fa-solid fa-search"></i></span>
                <input type="text" name="search" class="form-control" placeholder="Cari NIK, Nama, No. KK, atau Alamat..." value="{{ request('search') }}">
            </div>
        </div>
        <div class="col-md-3">
            <select name="gender" class="form-select form-select-sm">
                <option value="">-- Semua Jenis Kelamin --</option>
                <option value="LAKI_LAKI" {{ request('gender') == 'LAKI_LAKI' ? 'selected' : '' }}>Laki-laki</option>
                <option value="PEREMPUAN" {{ request('gender') == 'PEREMPUAN' ? 'selected' : '' }}>Perempuan</option>
            </select>
        </div>
        <div class="col-md-2">
            <select name="status" class="form-select form-select-sm">
                <option value="">-- Status --</option>
                <option value="TETAP" {{ request('status') == 'TETAP' ? 'selected' : '' }}>Tetap</option>
                <option value="PINDAH" {{ request('status') == 'PINDAH' ? 'selected' : '' }}>Pindah</option>
                <option value="MENINGGAL" {{ request('status') == 'MENINGGAL' ? 'selected' : '' }}>Meninggal</option>
            </select>
        </div>
        <div class="col-md-2 d-flex gap-1">
            <button type="submit" class="btn btn-primary btn-sm w-100">Filter</button>
            <a href="{{ route('citizens.index') }}" class="btn btn-secondary btn-sm">Reset</a>
        </div>
    </form>
</div>

<!-- Table Card -->
<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light small">
                <tr>
                    <th>NIK</th>
                    <th>Nama Lengkap</th>
                    <th>JK</th>
                    <th>TTL</th>
                    <th>Pekerjaan</th>
                    <th>Alamat / RT / RW</th>
                    <th>Status</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody class="small">
                @forelse($citizens as $c)
                <tr>
                    <td class="fw-semibold"><code>{{ $c->nik }}</code></td>
                    <td>
                        <a href="{{ route('citizens.show', $c->uuid) }}" class="text-decoration-none fw-bold text-dark">
                            {{ $c->name }}
                        </a>
                        @if($c->no_kk)
                            <div class="text-muted" style="font-size: 0.75rem;">KK: {{ $c->no_kk }}</div>
                        @endif
                    </td>
                    <td>{{ $c->gender == 'LAKI_LAKI' ? 'L' : 'P' }}</td>
                    <td>{{ $c->birth_place ?? '-' }}, {{ $c->birth_date ? $c->birth_date->format('d/m/Y') : '-' }}</td>
                    <td>{{ $c->occupation ?? '-' }}</td>
                    <td>{{ $c->address ?? '-' }} (RT {{ $c->rt ?? '-' }}/RW {{ $c->rw ?? '-' }})</td>
                    <td>
                        <span class="badge bg-{{ $c->status == 'TETAP' ? 'success' : ($c->status == 'PINDAH' ? 'warning' : 'secondary') }}">
                            {{ $c->status }}
                        </span>
                    </td>
                    <td class="text-end">
                        <div class="btn-group btn-group-sm">
                            <a href="{{ route('citizens.show', $c->uuid) }}" class="btn btn-light" title="Detail"><i class="fa-solid fa-eye text-primary"></i></a>
                            <a href="{{ route('citizens.edit', $c->uuid) }}" class="btn btn-light" title="Ubah"><i class="fa-solid fa-pen-to-square text-warning"></i></a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center text-muted py-4">Data penduduk tidak ditemukan.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer bg-white d-flex justify-content-between align-items-center py-2">
        <small class="text-muted">Menampilkan {{ $citizens->count() }} dari {{ $citizens->total() }} data</small>
        {{ $citizens->links('pagination::bootstrap-5') }}
    </div>
</div>
@endsection
