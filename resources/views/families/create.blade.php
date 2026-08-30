@extends('layouts.app')

@section('title', 'Tambah Kartu Keluarga')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1">Tambah Kartu Keluarga (KK) Baru</h4>
        <p class="text-muted small mb-0">Input nomor KK dan identitas kepala keluarga.</p>
    </div>
    <a href="{{ route('families.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="fa-solid fa-arrow-left me-1"></i> Kembali
    </a>
</div>

<div class="card">
    <form action="{{ route('families.store') }}" method="POST" class="card-body p-4">
        @csrf
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label small fw-semibold">Nomor Kartu Keluarga (No. KK) *</label>
                <input type="text" name="no_kk" class="form-control" maxlength="16" required placeholder="16 digit Nomor KK" value="{{ old('no_kk') }}">
            </div>
            <div class="col-md-6">
                <label class="form-label small fw-semibold">Nama Kepala Keluarga *</label>
                <input type="text" name="head_name" class="form-control" required placeholder="Nama Lengkap Kepala Keluarga" value="{{ old('head_name') }}">
            </div>
            <div class="col-md-6">
                <label class="form-label small fw-semibold">NIK Kepala Keluarga</label>
                <input type="text" name="head_nik" class="form-control" maxlength="16" placeholder="16 digit NIK Kepala Keluarga" value="{{ old('head_nik') }}">
            </div>
            <div class="col-md-6">
                <label class="form-label small fw-semibold">Tanggal Terbit KK</label>
                <input type="date" name="issue_date" class="form-control" value="{{ old('issue_date') }}">
            </div>
            <div class="col-md-8">
                <label class="form-label small fw-semibold">Alamat Keluarga</label>
                <input type="text" name="address" class="form-control" placeholder="Alamat jalan / kampung" value="{{ old('address') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-semibold">RT</label>
                <input type="text" name="rt" class="form-control" placeholder="001" value="{{ old('rt') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-semibold">RW</label>
                <input type="text" name="rw" class="form-control" placeholder="002" value="{{ old('rw') }}">
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
            <a href="{{ route('families.index') }}" class="btn btn-light px-4">Batal</a>
            <button type="submit" class="btn btn-primary px-4">
                <i class="fa-solid fa-save me-1"></i> Simpan Kartu Keluarga
            </button>
        </div>
    </form>
</div>
@endsection
