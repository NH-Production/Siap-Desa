@extends('layouts.app')

@section('title', 'Tambah Aset Desa')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1">Registrasi Aset Baru</h4>
        <p class="text-muted small mb-0">Input rincian aset milik desa.</p>
    </div>
    <a href="{{ route('assets.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="fa-solid fa-arrow-left me-1"></i> Kembali
    </a>
</div>

<div class="card">
    <form action="{{ route('assets.store') }}" method="POST" class="card-body p-4">
        @csrf
        <div class="row g-3">
            <div class="col-md-8">
                <label class="form-label small fw-semibold">Nama Barang / Aset *</label>
                <input type="text" name="name" class="form-control" required placeholder="Contoh: Genset Honda 5000W">
            </div>
            <div class="col-md-4">
                <label class="form-label small fw-semibold">Kategori Golongan *</label>
                <select name="category_id" class="form-select" required>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label small fw-semibold">Tanggal Perolehan</label>
                <input type="date" name="acquisition_date" class="form-control" value="{{ date('Y-m-d') }}">
            </div>
            <div class="col-md-4">
                <label class="form-label small fw-semibold">Sumber Dana / Perolehan</label>
                <input type="text" name="acquisition_source" class="form-control" placeholder="Contoh: APBDes 2026, Hibah">
            </div>
            <div class="col-md-4">
                <label class="form-label small fw-semibold">Nilai Perolehan (Rp) *</label>
                <input type="number" step="0.01" name="acquisition_cost" class="form-control" required placeholder="0">
            </div>
            <div class="col-md-4">
                <label class="form-label small fw-semibold">Kondisi Fisik *</label>
                <select name="condition" class="form-select" required>
                    <option value="BAIK">BAIK</option>
                    <option value="RUSAK_RINGAN">RUSAK RINGAN</option>
                    <option value="RUSAK_BERAT">RUSAK BERAT</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label small fw-semibold">Lokasi Penempatan</label>
                <input type="text" name="location" class="form-control" placeholder="Contoh: Ruang Pelayanan">
            </div>
            <div class="col-md-4">
                <label class="form-label small fw-semibold">Penanggung Jawab (Kustodian)</label>
                <input type="text" name="custodian" class="form-control" placeholder="Nama pegawai / seksi">
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
            <a href="{{ route('assets.index') }}" class="btn btn-light px-4">Batal</a>
            <button type="submit" class="btn btn-primary px-4">
                <i class="fa-solid fa-save me-1"></i> Daftarkan Aset
            </button>
        </div>
    </form>
</div>
@endsection
