@extends('layouts.app')

@section('title', 'Audit Trail Log')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1">Audit Trail & Security Logs</h4>
        <p class="text-muted small mb-0">Pencatatan mutasi data, login, operasi cetak, dan sinkronisasi.</p>
    </div>
    <a href="{{ route('system.diagnostics') }}" class="btn btn-outline-secondary btn-sm">
        <i class="fa-solid fa-arrow-left me-1"></i> Kembali ke Diagnostik
    </a>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light small">
                <tr>
                    <th>Waktu</th>
                    <th>User</th>
                    <th>Aksi</th>
                    <th>Tabel / Modul</th>
                    <th>IP Address</th>
                </tr>
            </thead>
            <tbody class="small">
                @forelse($logs as $log)
                <tr>
                    <td>{{ $log->created_at->format('d/m/Y H:i:s') }}</td>
                    <td><strong>{{ $log->user->name ?? 'System' }}</strong></td>
                    <td>
                        <span class="badge bg-{{ in_array($log->action, ['CREATE', 'LOGIN']) ? 'success' : (in_array($log->action, ['UPDATE', 'APPROVE']) ? 'primary' : 'secondary') }}">
                            {{ $log->action }}
                        </span>
                    </td>
                    <td><code>{{ $log->table_name ?? '-' }}</code></td>
                    <td>{{ $log->ip_address ?? '127.0.0.1' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center text-muted py-4">Belum ada riwayat audit log.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer bg-white py-2">
        {{ $logs->links('pagination::bootstrap-5') }}
    </div>
</div>
@endsection
