@extends('layouts.app')

@section('title', 'Conflict Center')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1">Conflict Center</h4>
        <p class="text-muted small mb-0">Resolusi perbedaan data multi-perangkat jika base version lokal tidak cocok dengan central version.</p>
    </div>
    <a href="{{ route('sync.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="fa-solid fa-arrow-left me-1"></i> Kembali ke Sync
    </a>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light small">
                <tr>
                    <th>Tabel</th>
                    <th>Record UUID</th>
                    <th>Server Version</th>
                    <th>Local Version</th>
                    <th>Status Resolusi</th>
                    <th class="text-end">Aksi Resolusi</th>
                </tr>
            </thead>
            <tbody class="small">
                @forelse($conflicts as $c)
                <tr>
                    <td><code>{{ $c->table_name }}</code></td>
                    <td><code>{{ $c->record_uuid }}</code></td>
                    <td>v{{ $c->server_version }}</td>
                    <td>v{{ $c->local_version }}</td>
                    <td>
                        @if($c->resolved_at)
                            <span class="badge bg-success">Terselesaikan ({{ $c->resolution }})</span>
                        @else
                            <span class="badge bg-danger">Butuh Resolusi</span>
                        @endif
                    </td>
                    <td class="text-end">
                        @if(!$c->resolved_at)
                        <form action="{{ route('sync.conflicts.resolve', $c->uuid) }}" method="POST" class="d-inline-flex gap-1">
                            @csrf
                            <button name="resolution" value="SERVER_WINS" class="btn btn-xs btn-outline-primary">Use Server</button>
                            <button name="resolution" value="LOCAL_WINS" class="btn btn-xs btn-outline-success">Use Local</button>
                        </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center text-muted py-4">Tidak ada konflik data yang memerlukan tindakan.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
