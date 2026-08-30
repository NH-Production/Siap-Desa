@extends('layouts.app')

@section('title', 'Detail Penduduk - ' . $citizen->name)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1">{{ $citizen->name }}</h4>
        <p class="text-muted small mb-0">NIK: <code>{{ $citizen->nik }}</code> • Global UUID: <code>{{ $citizen->uuid }}</code></p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('citizens.edit', $citizen->uuid) }}" class="btn btn-warning btn-sm">
            <i class="fa-solid fa-edit me-1"></i> Ubah Data
        </a>
        <a href="{{ route('citizens.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="fa-solid fa-arrow-left me-1"></i> Kembali
        </a>
    </div>
</div>

<div class="row g-4">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header"><i class="fa-solid fa-id-card me-2 text-primary"></i>Biodata Lengkap</div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-sm-6">
                        <small class="text-muted d-block">Nomor Induk Kependudukan (NIK)</small>
                        <strong class="fs-6">{{ $citizen->nik }}</strong>
                    </div>
                    <div class="col-sm-6">
                        <small class="text-muted d-block">Nomor Kartu Keluarga (No. KK)</small>
                        <strong>{{ $citizen->no_kk ?? '-' }}</strong>
                    </div>
                    <div class="col-sm-6">
                        <small class="text-muted d-block">Tempat, Tanggal Lahir</small>
                        <span>{{ $citizen->birth_place ?? '-' }}, {{ $citizen->birth_date ? $citizen->birth_date->format('d F Y') : '-' }}</span>
                    </div>
                    <div class="col-sm-6">
                        <small class="text-muted d-block">Jenis Kelamin</small>
                        <span>{{ $citizen->gender == 'LAKI_LAKI' ? 'Laki-laki' : 'Perempuan' }}</span>
                    </div>
                    <div class="col-sm-4">
                        <small class="text-muted d-block">Agama</small>
                        <span>{{ $citizen->religion ?? '-' }}</span>
                    </div>
                    <div class="col-sm-4">
                        <small class="text-muted d-block">Status Kawin</small>
                        <span>{{ $citizen->marital_status ?? '-' }}</span>
                    </div>
                    <div class="col-sm-4">
                        <small class="text-muted d-block">Golongan Darah</small>
                        <span>{{ $citizen->blood_type ?? '-' }}</span>
                    </div>
                    <div class="col-sm-6">
                        <small class="text-muted d-block">Pekerjaan</small>
                        <span>{{ $citizen->occupation ?? '-' }}</span>
                    </div>
                    <div class="col-sm-6">
                        <small class="text-muted d-block">Pendidikan Terakhir</small>
                        <span>{{ $citizen->education ?? '-' }}</span>
                    </div>
                    <div class="col-12">
                        <small class="text-muted d-block">Alamat Lengkap</small>
                        <span>{{ $citizen->address ?? '-' }} (RT {{ $citizen->rt ?? '-' }} / RW {{ $citizen->rw ?? '-' }})</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card mb-3">
            <div class="card-header"><i class="fa-solid fa-people-roof me-2 text-primary"></i>Hubungan Keluarga</div>
            <div class="card-body small">
                @if($citizen->familyMember && $citizen->familyMember->family)
                    <div class="mb-2">Status dalam KK: <span class="badge bg-primary">{{ $citizen->familyMember->relation_status }}</span></div>
                    <div class="mb-2">Kepala Keluarga: <strong>{{ $citizen->familyMember->family->head_name }}</strong></div>
                    <a href="{{ route('families.show', $citizen->familyMember->family->uuid) }}" class="btn btn-sm btn-outline-primary w-100 mt-2">
                        Lihat Kartu Keluarga
                    </a>
                @else
                    <p class="text-muted mb-0">Belum terhubung ke Kartu Keluarga terdaftar.</p>
                @endif
            </div>
        </div>

        <div class="card">
            <div class="card-header"><i class="fa-solid fa-clock-rotate-left me-2 text-primary"></i>Metadata & Sync</div>
            <div class="card-body small">
                <div class="mb-1 text-muted">Versi Record Lokal: <strong>v{{ $citizen->version }}</strong></div>
                <div class="mb-1 text-muted">Dibuat: {{ $citizen->created_at->format('d/m/Y H:i') }}</div>
                <div class="text-muted">Terakhir Update: {{ $citizen->updated_at->format('d/m/Y H:i') }}</div>
            </div>
        </div>
    </div>
</div>
@endsection
