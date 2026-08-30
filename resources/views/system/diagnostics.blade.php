@extends('layouts.app')

@section('title', 'Diagnostik Sistem')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1">Diagnostik & Status Kesehatan Sistem</h4>
        <p class="text-muted small mb-0">Pemeriksaan integritas runtime lokal, database, sinkronisasi, dan storage.</p>
    </div>
    <a href="{{ route('system.audit') }}" class="btn btn-outline-primary btn-sm">
        <i class="fa-solid fa-list-ul me-1"></i> Lihat Audit Trail Log
    </a>
</div>

<div class="row g-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header"><i class="fa-solid fa-server me-2 text-primary"></i>Status Runtime Lokal</div>
            <div class="card-body">
                <table class="table table-sm table-borderless mb-0 small">
                    <tr>
                        <td class="text-muted" style="width: 200px;">Versi Aplikasi SIAP:</td>
                        <td><span class="badge bg-primary">v{{ $health['app_version'] }}</span></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Versi Skema & Protokol:</td>
                        <td>Schema v{{ $health['schema_version'] }} • Protocol v{{ $health['sync_protocol_version'] }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">PHP Runtime:</td>
                        <td>PHP {{ $health['php_version'] }} (Win32 x64)</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Database Engine:</td>
                        <td><strong>{{ strtoupper($health['db_driver']) }}</strong> (<span class="text-success fw-bold">{{ $health['db_status'] }}</span>)</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Direktori Data Program:</td>
                        <td><code>{{ $health['data_path'] }}</code></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Sisa Ruang Disk:</td>
                        <td>{{ $health['disk_free_gb'] }} GB bebas dari {{ $health['disk_total_gb'] }} GB</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card">
            <div class="card-header"><i class="fa-solid fa-laptop-code me-2 text-primary"></i>Status Perangkat & Desa</div>
            <div class="card-body">
                <table class="table table-sm table-borderless mb-0 small">
                    <tr>
                        <td class="text-muted" style="width: 200px;">Nama Desa Terdaftar:</td>
                        <td><strong>{{ $health['village_name'] }}</strong></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Kode Perangkat Lokal:</td>
                        <td><code>{{ $health['device_code'] }}</code> (<span class="badge bg-success">{{ $health['device_status'] }}</span>)</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Antrian Sync Pending:</td>
                        <td><span class="badge bg-secondary">{{ $health['pending_push'] }} item</span></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Konflik Data:</td>
                        <td><span class="badge bg-{{ $health['conflicts'] > 0 ? 'danger' : 'success' }}">{{ $health['conflicts'] }} item</span></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
