@extends('layouts.central')

@section('title', 'Live Telemetri Sinkronisasi')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1">Live Telemetri Sinkronisasi Cloud</h4>
        <p class="text-muted small mb-0">Log aktivitas transmisi data Push & Pull dari seluruh stasiun komputer desa.</p>
    </div>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light small">
                <tr>
                    <th>Waktu</th>
                    <th>Kode Desa</th>
                    <th>Device ID</th>
                    <th>Arah Transmisi</th>
                    <th>Jumlah Record</th>
                    <th>Latensi</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody class="small">
                @forelse($logs as $l)
                <tr>
                    <td>{{ $l->created_at->format('d/m/Y H:i:s') }}</td>
                    <td><strong>{{ $l->village_code }}</strong></td>
                    <td><code>{{ $l->device_code }}</code></td>
                    <td>
                        <span class="badge bg-{{ $l->direction == 'PUSH' ? 'primary' : 'info' }}">
                            <i class="fa-solid fa-arrow-{{ $l->direction == 'PUSH' ? 'up' : 'down' }} me-1"></i> {{ $l->direction }}
                        </span>
                    </td>
                    <td>{{ $l->records_count }} mutasi</td>
                    <td><code>{{ $l->latency_ms }}ms</code></td>
                    <td><span class="badge bg-success">{{ $l->status }}</span></td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center text-muted py-4">Belum ada riwayat aktivitas telemetri sync tercatat.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
