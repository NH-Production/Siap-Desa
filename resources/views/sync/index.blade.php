@extends('layouts.app')

@section('title', 'Sync Center')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1">Sync Center & Central Cloud</h4>
        <p class="text-muted small mb-0">Delta Synchronization antara MariaDB/SQLite Lokal dan Central Supabase PostgreSQL.</p>
    </div>
    <div class="d-flex gap-2">
        <form action="{{ route('sync.push') }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-outline-primary btn-sm">
                <i class="fa-solid fa-arrow-up-from-bracket me-1"></i> Push (Kirim)
            </button>
        </form>
        <form action="{{ route('sync.pull') }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-outline-success btn-sm">
                <i class="fa-solid fa-arrow-down-to-bracket me-1"></i> Pull (Tarik)
            </button>
        </form>
        <form action="{{ route('sync.full') }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-primary btn-sm">
                <i class="fa-solid fa-rotate me-1"></i> Sinkronisasi Penuh
            </button>
        </form>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card p-3">
            <small class="text-muted fw-semibold">Pending Queue (Kirim)</small>
            <h3 class="fw-bold text-primary mb-0">{{ $totalPending }}</h3>
            <small class="text-muted">Perubahan Lokal</small>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card p-3">
            <small class="text-muted fw-semibold">Total Synced</small>
            <h3 class="fw-bold text-success mb-0">{{ $totalSynced }}</h3>
            <small class="text-muted">Sukses Terkirim</small>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card p-3">
            <small class="text-muted fw-semibold">Failed Retries</small>
            <h3 class="fw-bold text-danger mb-0">{{ $totalFailed }}</h3>
            <small class="text-muted">Gagal</small>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card p-3">
            <small class="text-muted fw-semibold">Konflik Data</small>
            <h3 class="fw-bold text-warning mb-0">{{ $totalConflicts }}</h3>
            <a href="{{ route('sync.conflicts') }}" class="small text-decoration-none">Buka Conflict Center &rarr;</a>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="fa-solid fa-list-check me-2 text-primary"></i>Sync Queue Aktif (Local Outbox)</span>
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light small">
                <tr>
                    <th>Tabel</th>
                    <th>Operasi</th>
                    <th>Record UUID</th>
                    <th>Versi</th>
                    <th>Status</th>
                    <th>Waktu Masuk Queue</th>
                </tr>
            </thead>
            <tbody class="small">
                @forelse($pendingItems as $q)
                <tr>
                    <td><code>{{ $q->table_name }}</code></td>
                    <td>
                        <span class="badge bg-{{ $q->operation == 'INSERT' ? 'success' : ($q->operation == 'UPDATE' ? 'primary' : 'danger') }}">
                            {{ $q->operation }}
                        </span>
                    </td>
                    <td><code>{{ $q->record_uuid }}</code></td>
                    <td>v{{ $q->local_version }}</td>
                    <td><span class="badge bg-warning text-dark">{{ $q->status }}</span></td>
                    <td>{{ $q->created_at->diffForHumans() }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center text-muted py-4">Antrian sync lokal bersih (0 pending).</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
