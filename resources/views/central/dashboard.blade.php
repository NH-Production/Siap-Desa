@extends('layouts.central')

@section('title', 'Dashboard Server Utama')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1">Pusat Komando SIAP Desa Cloud (SAAS Server)</h4>
        <p class="text-muted small mb-0">Monitoring multi-tenant desa, validasi lisensi real-time, dan status telemetri sinkronisasi.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('central.licenses.index') }}" class="btn btn-primary btn-sm">
            <i class="fa-solid fa-plus me-1"></i> Buat Lisensi Baru
        </a>
        <a href="{{ route('central.releases.index') }}" class="btn btn-outline-primary btn-sm">
            <i class="fa-solid fa-cloud-arrow-up me-1"></i> Rilis Patch OTA
        </a>
    </div>
</div>

<!-- Metrics Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card p-3">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-muted small d-block">Desa Terdaftar</span>
                    <h3 class="fw-bold text-dark mb-0">{{ $totalVillages }}</h3>
                </div>
                <div class="p-3 bg-primary bg-opacity-10 text-primary rounded-circle">
                    <i class="fa-solid fa-tree-city fs-4"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card p-3">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-muted small d-block">Lisensi Aktif</span>
                    <h3 class="fw-bold text-success mb-0">{{ $activeLicenses }}</h3>
                </div>
                <div class="p-3 bg-success bg-opacity-10 text-success rounded-circle">
                    <i class="fa-solid fa-key fs-4"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card p-3">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-muted small d-block">Perangkat Terkoneksi</span>
                    <h3 class="fw-bold text-info mb-0">{{ $totalDevices }}</h3>
                </div>
                <div class="p-3 bg-info bg-opacity-10 text-info rounded-circle">
                    <i class="fa-solid fa-laptop fs-4"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card p-3">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-muted small d-block">Aktivitas Sync Hari Ini</span>
                    <h3 class="fw-bold text-warning mb-0">{{ $todaySyncs }}</h3>
                </div>
                <div class="p-3 bg-warning bg-opacity-10 text-warning rounded-circle">
                    <i class="fa-solid fa-rotate fs-4"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Active Villages -->
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="fa-solid fa-building-flag me-2 text-primary"></i>Desa Terdaftar Terbaru</span>
                <a href="{{ route('central.villages.index') }}" class="btn btn-link btn-sm p-0">Lihat Semua</a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0 small">
                    <thead class="table-light">
                        <tr>
                            <th>Kode Desa</th>
                            <th>Nama Desa</th>
                            <th>Wilayah</th>
                            <th>Status Lisensi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($villages as $v)
                        <tr>
                            <td><code>{{ $v->code }}</code></td>
                            <td class="fw-bold">{{ $v->name }}</td>
                            <td>{{ $v->district }}, {{ $v->regency }}</td>
                            <td>
                                @if($v->activeLicense)
                                    <span class="badge bg-success">Active Enterprise</span>
                                @else
                                    <span class="badge bg-secondary">No Active License</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted py-4">Belum ada desa yang terdaftar.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Live Telemetry Sync Logs -->
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="fa-solid fa-satellite-dish me-2 text-success"></i>Live Traffic Sinkronisasi</span>
                <a href="{{ route('central.telemetry') }}" class="btn btn-link btn-sm p-0">Semua Log</a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0 small">
                    <thead class="table-light">
                        <tr>
                            <th>Waktu</th>
                            <th>Desa</th>
                            <th>Aksi</th>
                            <th>Records</th>
                            <th>Latency</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentSyncs as $log)
                        <tr>
                            <td>{{ $log->created_at->format('H:i:s') }}</td>
                            <td><strong>{{ $log->village_code }}</strong></td>
                            <td><span class="badge bg-{{ $log->direction == 'PUSH' ? 'primary' : 'info' }}">{{ $log->direction }}</span></td>
                            <td>{{ $log->records_count }} item</td>
                            <td><code>{{ $log->latency_ms }}ms</code></td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">Menunggu traffic sinkronisasi dari klien desa...</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
