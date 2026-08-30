@extends('layouts.central')

@section('title', 'Tambah Desa Baru')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1">Registrasi Tenant Desa Baru</h4>
        <p class="text-muted small mb-0">Input data identitas desa dan generate lisensi aktivasi otomatis.</p>
    </div>
    <a href="{{ route('central.villages.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="fa-solid fa-arrow-left me-1"></i> Kembali
    </a>
</div>

<div class="card">
    <form action="{{ route('central.villages.store') }}" method="POST" class="card-body p-4">
        @csrf
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label small fw-semibold">Kode Desa (Kemendagri) *</label>
                <input type="text" name="code" class="form-control" required placeholder="Contoh: 3203162002">
            </div>
            <div class="col-md-6">
                <label class="form-label small fw-semibold">Nama Desa *</label>
                <input type="text" name="name" class="form-control" required placeholder="Contoh: Sindangresmi">
            </div>
            <div class="col-md-4">
                <label class="form-label small fw-semibold">Kecamatan *</label>
                <input type="text" name="district" class="form-control" required placeholder="Contoh: Takokak">
            </div>
            <div class="col-md-4">
                <label class="form-label small fw-semibold">Kabupaten / Kota *</label>
                <input type="text" name="regency" class="form-control" required placeholder="Contoh: Kabupaten Cianjur">
            </div>
            <div class="col-md-4">
                <label class="form-label small fw-semibold">Provinsi *</label>
                <input type="text" name="province" class="form-control" required placeholder="Contoh: Jawa Barat">
            </div>
            <div class="col-md-6">
                <label class="form-label small fw-semibold">Nama Kepala Desa</label>
                <input type="text" name="head_name" class="form-control" placeholder="Contoh: IMAS, S.IP., NL.P">
            </div>
            <div class="col-md-6">
                <label class="form-label small fw-semibold">Status Operasional *</label>
                <select name="status" class="form-select" required>
                    <option value="ACTIVE">ACTIVE (Aktif)</option>
                    <option value="TRIAL">TRIAL (Uji Coba)</option>
                    <option value="SUSPENDED">SUSPENDED (Ditangguhkan)</option>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label small fw-semibold">Nomor Telepon / WhatsApp</label>
                <input type="text" name="phone" class="form-control" placeholder="0812...">
            </div>
            <div class="col-md-6">
                <label class="form-label small fw-semibold">Email Kantor Desa</label>
                <input type="email" name="email" class="form-control" placeholder="pemdes@desa.id">
            </div>
            <div class="col-12">
                <label class="form-label small fw-semibold">Alamat Kantor Desa</label>
                <input type="text" name="address" class="form-control" placeholder="Jl. Raya Desa No. ...">
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
            <a href="{{ route('central.villages.index') }}" class="btn btn-light px-4">Batal</a>
            <button type="submit" class="btn btn-primary px-4">
                <i class="fa-solid fa-save me-1"></i> Daftarkan Desa & Generate Lisensi
            </button>
        </div>
    </form>
</div>
@endsection
