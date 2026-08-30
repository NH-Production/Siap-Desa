@extends('layouts.app')

@section('title', 'Tambah Penduduk')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1">Tambah Data Penduduk</h4>
        <p class="text-muted small mb-0">Masukkan biodata warga desa sesuai KTP / Kartu Keluarga.</p>
    </div>
    <a href="{{ route('citizens.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="fa-solid fa-arrow-left me-1"></i> Kembali
    </a>
</div>

<div class="card">
    <form action="{{ route('citizens.store') }}" method="POST" class="card-body p-4">
        @csrf
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label small fw-semibold">Nomor Induk Kependudukan (NIK) *</label>
                <input type="text" name="nik" class="form-control" maxlength="16" required placeholder="16 digit NIK" value="{{ old('nik') }}">
            </div>
            <div class="col-md-6">
                <label class="form-label small fw-semibold">Nomor Kartu Keluarga (No. KK)</label>
                <input type="text" name="no_kk" class="form-control" maxlength="16" placeholder="16 digit No. KK" value="{{ old('no_kk') }}">
            </div>

            <div class="col-md-8">
                <label class="form-label small fw-semibold">Nama Lengkap Sesuai KTP *</label>
                <input type="text" name="name" class="form-control" required placeholder="Nama Lengkap" value="{{ old('name') }}">
            </div>
            <div class="col-md-4">
                <label class="form-label small fw-semibold">Jenis Kelamin *</label>
                <select name="gender" class="form-select" required>
                    <option value="LAKI_LAKI" {{ old('gender') == 'LAKI_LAKI' ? 'selected' : '' }}>Laki-laki</option>
                    <option value="PEREMPUAN" {{ old('gender') == 'PEREMPUAN' ? 'selected' : '' }}>Perempuan</option>
                </select>
            </div>

            <div class="col-md-4">
                <label class="form-label small fw-semibold">Tempat Lahir</label>
                <input type="text" name="birth_place" class="form-control" placeholder="Kota / Kab. Lahir" value="{{ old('birth_place') }}">
            </div>
            <div class="col-md-4">
                <label class="form-label small fw-semibold">Tanggal Lahir</label>
                <input type="date" name="birth_date" class="form-control" value="{{ old('birth_date') }}">
            </div>
            <div class="col-md-4">
                <label class="form-label small fw-semibold">Golongan Darah</label>
                <select name="blood_type" class="form-select">
                    <option value="">-- Tidak Tahu --</option>
                    <option value="A">A</option>
                    <option value="B">B</option>
                    <option value="AB">AB</option>
                    <option value="O">O</option>
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label small fw-semibold">Agama</label>
                <select name="religion" class="form-select">
                    <option value="ISLAM">Islam</option>
                    <option value="KRISTEN">Kristen</option>
                    <option value="KATOLIK">Katolik</option>
                    <option value="HINDU">Hindu</option>
                    <option value="BUDDHA">Buddha</option>
                    <option value="KONGHUCU">Konghucu</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-semibold">Status Perkawinan</label>
                <select name="marital_status" class="form-select">
                    <option value="BELUM_KAWIN">Belum Kawin</option>
                    <option value="KAWIN">Kawin</option>
                    <option value="CERAI_HIDUP">Cerai Hidup</option>
                    <option value="CERAI_MATI">Cerai Mati</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-semibold">Pekerjaan</label>
                <input type="text" name="occupation" class="form-control" placeholder="Contoh: Wiraswasta, Petani, PNS" value="{{ old('occupation') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-semibold">Pendidikan Terakhir</label>
                <input type="text" name="education" class="form-control" placeholder="Contoh: SMA, S1" value="{{ old('education') }}">
            </div>

            <div class="col-md-8">
                <label class="form-label small fw-semibold">Alamat Domisili</label>
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

            <div class="col-md-4">
                <label class="form-label small fw-semibold">Status Kependudukan *</label>
                <select name="status" class="form-select" required>
                    <option value="TETAP">Penduduk Tetap</option>
                    <option value="PINDAH">Pindah Keluar</option>
                    <option value="MENINGGAL">Meninggal Dunia</option>
                    <option value="SEMENTARA">Penduduk Sementara</option>
                </select>
            </div>
            <div class="col-md-8">
                <label class="form-label small fw-semibold">Catatan Tambahan</label>
                <input type="text" name="notes" class="form-control" placeholder="Keterangan khusus jika ada" value="{{ old('notes') }}">
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
            <a href="{{ route('citizens.index') }}" class="btn btn-light px-4">Batal</a>
            <button type="submit" class="btn btn-primary px-4">
                <i class="fa-solid fa-save me-1"></i> Simpan Data Penduduk
            </button>
        </div>
    </form>
</div>
@endsection
