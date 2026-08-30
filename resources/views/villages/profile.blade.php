@extends('layouts.app')

@section('title', 'Profil & Wilayah Desa')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1">Profil & Pengaturan Pemerintahan Desa</h4>
        <p class="text-muted small mb-0">Kelola identitas desa, penomoran surat, dan data pejabat desa.</p>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <i class="fa-solid fa-edit me-2 text-primary"></i>Informasi Umum Desa
            </div>
            <form action="{{ route('village.update') }}" method="POST" class="card-body">
                @csrf
                <div class="row g-3 mb-3">
                    <div class="col-md-8">
                        <label class="form-label small fw-semibold">Nama Desa</label>
                        <input type="text" name="name" class="form-control" value="{{ $village->name }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-semibold">Kode Desa</label>
                        <input type="text" name="code" class="form-control" value="{{ $village->code }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-semibold">Kecamatan</label>
                        <input type="text" name="district" class="form-control" value="{{ $village->district }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-semibold">Kabupaten / Kota</label>
                        <input type="text" name="regency" class="form-control" value="{{ $village->regency }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-semibold">Provinsi</label>
                        <input type="text" name="province" class="form-control" value="{{ $village->province }}">
                    </div>
                    <div class="col-md-8">
                        <label class="form-label small fw-semibold">Alamat Kantor Desa</label>
                        <input type="text" name="address" class="form-control" value="{{ $village->address }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-semibold">Kode Pos</label>
                        <input type="text" name="postal_code" class="form-control" value="{{ $village->postal_code }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-semibold">Nomor Telepon</label>
                        <input type="text" name="phone" class="form-control" value="{{ $village->phone }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-semibold">Email Resmi</label>
                        <input type="email" name="email" class="form-control" value="{{ $village->email }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-semibold">Website</label>
                        <input type="text" name="website" class="form-control" value="{{ $village->website }}">
                    </div>
                </div>

                <h6 class="fw-bold text-primary mt-4 mb-3"><i class="fa-solid fa-users-gear me-2"></i>Pimpinan & Penomoran Dokumen</h6>
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Nama Kepala Desa (Kades)</label>
                        <input type="text" name="head_name" class="form-control" value="{{ $village->head_name }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Nama Sekretaris Desa (Sekdes)</label>
                        <input type="text" name="secretary_name" class="form-control" value="{{ $village->secretary_name }}">
                    </div>
                    <div class="col-md-12">
                        <label class="form-label small fw-semibold">Format Penomoran Surat Resmi</label>
                        <input type="text" name="letter_number_format" class="form-control" value="{{ $village->letter_number_format }}" required>
                        <small class="text-muted">Tag yang didukung: {KODE}, {NO}, {BULAN_ROMAWI}, {TAHUN}</small>
                    </div>
                </div>

                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="fa-solid fa-save me-1"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card mb-4">
            <div class="card-header"><i class="fa-solid fa-fingerprint me-2 text-primary"></i>Identitas Sistem Global</div>
            <div class="card-body small">
                <div class="mb-2">
                    <span class="text-muted d-block">Global Village UUID:</span>
                    <code>{{ $village->uuid }}</code>
                </div>
                <div class="mb-2">
                    <span class="text-muted d-block">Database Local Engine:</span>
                    <strong>SQLite 3 / MariaDB Ready</strong>
                </div>
                <div>
                    <span class="text-muted d-block">Versi Skema Data:</span>
                    <span class="badge bg-primary">Schema v1 • App 1.0.0</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
