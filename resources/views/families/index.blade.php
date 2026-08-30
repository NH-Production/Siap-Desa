@extends('layouts.app')

@section('title', 'Kartu Keluarga')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1">Kartu Keluarga (KK)</h4>
        <p class="text-muted small mb-0">Data kartu keluarga dan relasi susunan anggota keluarga desa.</p>
    </div>
    <a href="{{ route('families.create') }}" class="btn btn-primary btn-sm">
        <i class="fa-solid fa-plus me-1"></i> Tambah KK Baru
    </a>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light small">
                <tr>
                    <th>Nomor KK</th>
                    <th>Kepala Keluarga</th>
                    <th>Alamat / RT / RW</th>
                    <th>Jml Anggota</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody class="small">
                @forelse($families as $f)
                <tr>
                    <td class="fw-semibold"><code>{{ $f->no_kk }}</code></td>
                    <td>
                        <a href="{{ route('families.show', $f->uuid) }}" class="text-decoration-none fw-bold text-dark">
                            {{ $f->head_name }}
                        </a>
                    </td>
                    <td>{{ $f->address ?? '-' }} (RT {{ $f->rt ?? '-' }}/RW {{ $f->rw ?? '-' }})</td>
                    <td><span class="badge bg-info-subtle text-info border">{{ $f->members->count() }} Jiwa</span></td>
                    <td class="text-end">
                        <a href="{{ route('families.show', $f->uuid) }}" class="btn btn-sm btn-light"><i class="fa-solid fa-eye text-primary"></i> Detail & Anggota</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center text-muted py-4">Belum ada data Kartu Keluarga.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
