@extends('layouts.app')

@section('title', 'Ubah Penduduk - ' . $citizen->name)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1">Ubah Data Penduduk</h4>
        <p class="text-muted small mb-0">Perbarui biodata warga <strong>{{ $citizen->name }}</strong> (NIK: {{ $citizen->nik }}).</p>
    </div>
    <a href="{{ route('citizens.show', $citizen->uuid) }}" class="btn btn-outline-secondary btn-sm">
        <i class="fa-solid fa-arrow-left me-1"></i> Kembali
    </a>
</div>

<div class="card">
    <form action="{{ route('citizens.update', $citizen->uuid) }}" method="POST" class="card-body p-4">
        @csrf
        @method('PUT')
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label small fw-semibold">NIK *</label>
                <input type="text" name="nik" class="form-control" maxlength="16" required value="{{ old('nik', $citizen->nik) }}">
            </div>
            <div class="col-md-6">
                <label class="form-label small fw-semibold">No. KK</label>
                <input type="text" name="no_kk" class="form-control" maxlength="16" value="{{ old('no_kk', $citizen->no_kk) }}">
            </div>
            <div class="col-md-8">
                <label class="form-label small fw-semibold">Nama Lengkap *</label>
                <input type="text" name="name" class="form-control" required value="{{ old('name', $citizen->name) }}">
            </div>
            <div class="col-md-4">
                <label class="form-label small fw-semibold">Jenis Kelamin *</label>
                <select name="gender" class="form-select" required>
                    <option value="LAKI_LAKI" {{ old('gender', $citizen->gender) == 'LAKI_LAKI' ? 'selected' : '' }}>Laki-laki</option>
                    <option value="PEREMPUAN" {{ old('gender', $citizen->gender) == 'PEREMPUAN' ? 'selected' : '' }}>Perempuan</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label small fw-semibold">Tempat Lahir</label>
                <input type="text" name="birth_place" class="form-control" value="{{ old('birth_place', $citizen->birth_place) }}">
            </div>
            <div class="col-md-4">
                <label class="form-label small fw-semibold">Tanggal Lahir</label>
                <input type="date" name="birth_date" class="form-control" value="{{ old('birth_date', $citizen->birth_date ? $citizen->birth_date->format('Y-m-d') : '') }}">
            </div>
            <div class="col-md-4">
                <label class="form-label small fw-semibold">Golongan Darah</label>
                <input type="text" name="blood_type" class="form-control" value="{{ old('blood_type', $citizen->blood_type) }}">
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-semibold">Agama</label>
                <input type="text" name="religion" class="form-control" value="{{ old('religion', $citizen->religion) }}">
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-semibold">Status Kawin</label>
                <input type="text" name="marital_status" class="form-control" value="{{ old('marital_status', $citizen->marital_status) }}">
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-semibold">Pekerjaan</label>
                <input type="text" name="occupation" class="form-control" value="{{ old('occupation', $citizen->occupation) }}">
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-semibold">Pendidikan</label>
                <input type="text" name="education" class="form-control" value="{{ old('education', $citizen->education) }}">
            </div>
            <div class="col-md-8">
                <label class="form-label small fw-semibold">Alamat</label>
                <input type="text" name="address" class="form-control" value="{{ old('address', $citizen->address) }}">
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-semibold">RT</label>
                <input type="text" name="rt" class="form-control" value="{{ old('rt', $citizen->rt) }}">
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-semibold">RW</label>
                <input type="text" name="rw" class="form-control" value="{{ old('rw', $citizen->rw) }}">
            </div>
            <div class="col-md-4">
                <label class="form-label small fw-semibold">Status *</label>
                <select name="status" class="form-select" required>
                    <option value="TETAP" {{ old('status', $citizen->status) == 'TETAP' ? 'selected' : '' }}>TETAP</option>
                    <option value="PINDAH" {{ old('status', $citizen->status) == 'PINDAH' ? 'selected' : '' }}>PINDAH</option>
                    <option value="MENINGGAL" {{ old('status', $citizen->status) == 'MENINGGAL' ? 'selected' : '' }}>MENINGGAL</option>
                    <option value="SEMENTARA" {{ old('status', $citizen->status) == 'SEMENTARA' ? 'selected' : '' }}>SEMENTARA</option>
                </select>
            </div>
            <div class="col-md-8">
                <label class="form-label small fw-semibold">Catatan</label>
                <input type="text" name="notes" class="form-control" value="{{ old('notes', $citizen->notes) }}">
            </div>
        </div>

        <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
            <button type="button" class="btn btn-outline-danger btn-sm" onclick="if(confirm('Hapus penduduk ini?')) document.getElementById('deleteForm').submit();">
                <i class="fa-solid fa-trash me-1"></i> Hapus Penduduk
            </button>
            <div class="d-flex gap-2">
                <a href="{{ route('citizens.show', $citizen->uuid) }}" class="btn btn-light px-4">Batal</a>
                <button type="submit" class="btn btn-primary px-4">
                    <i class="fa-solid fa-save me-1"></i> Simpan Perubahan
                </button>
            </div>
        </div>
    </form>

    <form id="deleteForm" action="{{ route('citizens.destroy', $citizen->uuid) }}" method="POST" style="display:none;">
        @csrf
        @method('DELETE')
    </form>
</div>
@endsection
