@extends('layouts.app')

@section('title', 'Administrasi Surat')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1">Administrasi & Penerbitan Surat</h4>
        <p class="text-muted small mb-0">Pembuatan surat keterangan, penomoran otomatis, approval kades, dan cetak PDF resmi.</p>
    </div>
    <a href="{{ route('letters.create') }}" class="btn btn-primary btn-sm">
        <i class="fa-solid fa-plus me-1"></i> Buat Surat Baru
    </a>
</div>

<div class="card mb-4 p-3 bg-light border">
    <form action="{{ route('letters.index') }}" method="GET" class="row g-2 align-items-center">
        <div class="col-md-5">
            <input type="text" name="search" class="form-control form-control-sm" placeholder="Cari nomor surat, nama pemohon, NIK..." value="{{ request('search') }}">
        </div>
        <div class="col-md-3">
            <select name="type" class="form-select form-select-sm">
                <option value="">-- Semua Jenis Surat --</option>
                @foreach($letterTypes as $lt)
                    <option value="{{ $lt->id }}" {{ request('type') == $lt->id ? 'selected' : '' }}>{{ $lt->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <select name="status" class="form-select form-select-sm">
                <option value="">-- Status --</option>
                <option value="DRAFT" {{ request('status') == 'DRAFT' ? 'selected' : '' }}>Draft</option>
                <option value="APPROVED" {{ request('status') == 'APPROVED' ? 'selected' : '' }}>Disetujui</option>
            </select>
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-primary btn-sm w-100">Filter</button>
        </div>
    </form>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light small">
                <tr>
                    <th>No. Surat Resmi / Draft</th>
                    <th>Pemohon</th>
                    <th>Jenis Surat</th>
                    <th>Tgl Pengajuan</th>
                    <th>Status</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody class="small">
                @forelse($letters as $l)
                <tr>
                    <td class="fw-semibold">
                        {{ $l->letter_number ?? $l->draft_number }}
                    </td>
                    <td>
                        <div class="fw-bold">{{ $l->applicant_name }}</div>
                        <small class="text-muted">NIK: {{ $l->applicant_nik ?? '-' }}</small>
                    </td>
                    <td><span class="badge bg-primary-subtle text-primary border">{{ $l->letterType->name ?? '-' }}</span></td>
                    <td>{{ $l->created_at->format('d/m/Y') }}</td>
                    <td>
                        <span class="badge bg-{{ $l->status == 'APPROVED' ? 'success' : 'warning text-dark' }}">
                            {{ $l->status == 'APPROVED' ? 'Disetujui' : 'Draft / Review' }}
                        </span>
                    </td>
                    <td class="text-end">
                        <div class="btn-group btn-group-sm">
                            <a href="{{ route('letters.show', $l->uuid) }}" class="btn btn-light" title="Detail"><i class="fa-solid fa-eye text-primary"></i></a>
                            <a href="{{ route('letters.print', $l->uuid) }}" class="btn btn-light" title="Cetak Surat" target="_blank"><i class="fa-solid fa-print text-success"></i></a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center text-muted py-4">Belum ada surat yang diterbitkan.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
