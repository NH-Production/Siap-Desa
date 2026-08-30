@extends('layouts.central')

@section('title', 'Dashboard Server Utama')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1">Pusat Komando SIAP CLOUD (Real-Time Master Portal)</h4>
        <p class="text-muted small mb-0">Monitoring multi-tenant desa, validasi lisensi real-time, dan transmisi delta sync online.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('central.settings.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="fa-solid fa-sliders me-1"></i> Pengaturan Server
        </a>
        <a href="{{ route('central.licenses.index') }}" class="btn btn-primary btn-sm">
            <i class="fa-solid fa-plus me-1"></i> Buat Lisensi Baru
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
                    <h3 class="fw-bold text-info mb-0" id="liveActiveDevices">{{ $totalDevices }}</h3>
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
                    <h3 class="fw-bold text-warning mb-0" id="liveTodaySyncs">{{ $todaySyncs }}</h3>
                </div>
                <div class="p-3 bg-warning bg-opacity-10 text-warning rounded-circle">
                    <i class="fa-solid fa-rotate fs-4"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Active Villages with Client ID -->
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="fa-solid fa-building-flag me-2 text-primary"></i>Desa Terdaftar & Client ID Unik</span>
                <a href="{{ route('central.villages.index') }}" class="btn btn-link btn-sm p-0">Lihat Semua</a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0 small">
                    <thead class="table-light">
                        <tr>
                            <th>Client ID</th>
                            <th>Nama Desa</th>
                            <th>Kode Lisensi</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($villages as $v)
                        <tr>
                            <td><code class="fw-bold text-indigo" style="color:#4f46e5;">{{ $v->client_id ?? 'CLNT-' . $v->code }}</code></td>
                            <td class="fw-bold">{{ $v->name }}</td>
                            <td>
                                @if($v->activeLicense)
                                    <code>{{ $v->activeLicense->license_key }}</code>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-success">Active Enterprise</span>
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

    <!-- Live Real-Time Telemetry Stream -->
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>
                    <span class="pulse-indicator me-1"></span>
                    <strong>Live Real-Time Sync Stream</strong>
                </span>
                <span class="badge bg-light text-dark border small" id="liveSyncStatus">Streaming Live...</span>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0 small">
                    <thead class="table-light">
                        <tr>
                            <th>Waktu</th>
                            <th>Desa</th>
                            <th>Arah</th>
                            <th>Records</th>
                            <th>Latensi</th>
                        </tr>
                    </thead>
                    <tbody id="liveSyncTableBody">
                        @forelse($recentSyncs as $log)
                        <tr>
                            <td>{{ $log->created_at->format('H:i:s') }}</td>
                            <td><strong>{{ $log->village_code }}</strong></td>
                            <td><span class="badge bg-{{ $log->direction == 'PUSH' ? 'primary' : 'info' }}">{{ $log->direction }}</span></td>
                            <td>{{ $log->records_count }} mutasi</td>
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

@push('scripts')
<script>
// Real-time live polling engine every 2.5 seconds
function pollLiveData() {
    fetch("{{ route('central.telemetry.live') }}")
        .then(res => res.json())
        .then(data => {
            document.getElementById('liveTodaySyncs').innerText = data.today_syncs;
            document.getElementById('liveActiveDevices').innerText = data.active_devices > 0 ? data.active_devices : data.total_devices;
            
            const tbody = document.getElementById('liveSyncTableBody');
            if (data.recent_logs && data.recent_logs.length > 0) {
                let html = '';
                data.recent_logs.forEach(log => {
                    const badgeClass = log.direction === 'PUSH' ? 'bg-primary' : 'bg-info';
                    html += `
                        <tr>
                            <td>${log.time}</td>
                            <td><strong>${log.village_code}</strong></td>
                            <td><span class="badge ${badgeClass}">${log.direction}</span></td>
                            <td>${log.records_count} mutasi</td>
                            <td><code>${log.latency_ms}ms</code></td>
                        </tr>
                    `;
                });
                tbody.innerHTML = html;
            }
        })
        .catch(e => console.log('Live poll error:', e));
}

setInterval(pollLiveData, 2500);
</script>
@endpush
