@extends('layouts.app')

@section('title', 'Pusat Laporan')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1">Pusat Laporan & Rekapitulasi</h4>
        <p class="text-muted small mb-0">Cetak dan unduh rekapitulasi data kependudukan, keuangan, absensi, dan aset.</p>
    </div>
</div>

<div class="row g-4">
    <div class="col-md-3">
        <div class="card p-4 text-center h-100">
            <i class="fa-solid fa-users fa-3x text-primary mb-3"></i>
            <h5 class="fw-bold">Laporan Penduduk</h5>
            <p class="text-muted small">Rekapitulasi demografi penduduk menurut jenis kelamin, agama, dan usia.</p>
            <a href="{{ route('reports.citizens') }}" class="btn btn-primary btn-sm mt-auto" target="_blank">
                <i class="fa-solid fa-file-pdf me-1"></i> Buka Laporan
            </a>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card p-4 text-center h-100">
            <i class="fa-solid fa-money-bill-trend-up fa-3x text-success mb-3"></i>
            <h5 class="fw-bold">Laporan Keuangan</h5>
            <p class="text-muted small">Rekapitulasi penerimaan, pengeluaran kas, dan realisasi anggaran APBDes.</p>
            <a href="{{ route('reports.finance') }}" class="btn btn-success btn-sm mt-auto" target="_blank">
                <i class="fa-solid fa-file-pdf me-1"></i> Buka Laporan
            </a>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card p-4 text-center h-100">
            <i class="fa-solid fa-user-check fa-3x text-info mb-3"></i>
            <h5 class="fw-bold">Laporan Absensi</h5>
            <p class="text-muted small">Rekapitulasi daftar hadir aparat & pegawai bulanan.</p>
            <a href="{{ route('reports.attendance') }}" class="btn btn-info text-white btn-sm mt-auto" target="_blank">
                <i class="fa-solid fa-file-pdf me-1"></i> Buka Laporan
            </a>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card p-4 text-center h-100">
            <i class="fa-solid fa-boxes-stacked fa-3x text-warning mb-3"></i>
            <h5 class="fw-bold">Laporan Inventaris</h5>
            <p class="text-muted small">Buku inventaris barang dan aset kekayaan milik desa.</p>
            <a href="{{ route('reports.assets') }}" class="btn btn-warning btn-sm mt-auto" target="_blank">
                <i class="fa-solid fa-file-pdf me-1"></i> Buka Laporan
            </a>
        </div>
    </div>
</div>
@endsection
