@extends('layouts.app')

@section('title', 'Tambah Pegawai Desa')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1">Tambah Aparat & Pegawai Desa</h4>
        <p class="text-muted small mb-0">Input data perangkat desa dan generate token absensi QR otomatis.</p>
    </div>
    <a href="{{ route('employees.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="fa-solid fa-arrow-left me-1"></i> Kembali
    </a>
</div>

<div class="card">
    <form action="{{ route('employees.store') }}" method="POST" class="card-body p-4">
        @csrf
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label small fw-semibold">Nama Lengkap Pegawai *</label>
                <input type="text" name="name" class="form-control" required placeholder="Nama Lengkap Beserta Gelar">
            </div>
            <div class="col-md-6">
                <label class="form-label small fw-semibold">Hubungkan dengan Data Penduduk</label>
                <select name="citizen_id" class="form-select">
                    <option value="">-- Pilih Penduduk (Opsional) --</option>
                    @foreach($citizens as $c)
                        <option value="{{ $c->id }}">{{ $c->name }} (NIK: {{ $c->nik }})</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label small fw-semibold">NIP / NIPD</label>
                <input type="text" name="nip" class="form-control" placeholder="Nomor Induk Pegawai">
            </div>
            <div class="col-md-4">
                <label class="form-label small fw-semibold">Jabatan *</label>
                <input type="text" name="position" class="form-control" required placeholder="Contoh: Kaur Keuangan, Kasi Pemerintahan">
            </div>
            <div class="col-md-4">
                <label class="form-label small fw-semibold">Status Kepegawaian *</label>
                <select name="employment_status" class="form-select" required>
                    <option value="PERANGKAT_DESA">Perangkat Desa</option>
                    <option value="KADES">Kepala Desa</option>
                    <option value="SEKDES">Sekretaris Desa</option>
                    <option value="HONORER">Staf Honorer / Kontrak</option>
                    <option value="PNS">PNS / ASN DPK</option>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label small fw-semibold">Unit Kerja / Bagian</label>
                <input type="text" name="department" class="form-control" placeholder="Contoh: Sekretariat Desa, Urusan Keuangan">
            </div>
            <div class="col-md-6">
                <label class="form-label small fw-semibold">Tanggal Mulai Bertugas</label>
                <input type="date" name="join_date" class="form-control" value="{{ date('Y-m-d') }}">
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
            <a href="{{ route('employees.index') }}" class="btn btn-light px-4">Batal</a>
            <button type="submit" class="btn btn-primary px-4">
                <i class="fa-solid fa-save me-1"></i> Simpan Pegawai & Buat Kartu QR
            </button>
        </div>
    </form>
</div>
@endsection
