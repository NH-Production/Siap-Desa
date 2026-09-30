@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="mb-1">Manajemen Device</h3>
            <div class="text-muted">Monitor komputer SIAP-DESA pada setiap desa.</div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card mb-4">
        <div class="card-header fw-semibold">Daftarkan Device</div>
        <div class="card-body">
            <form method="POST" action="{{ route('central.devices.register') }}" class="row g-3">
                @csrf
                <div class="col-md-4">
                    <label class="form-label">Desa</label>
                    <select name="village_uuid" class="form-select" required>
                        <option value="">Pilih desa</option>
                        @foreach($villages as $v)
                            <option value="{{ $v->uuid }}">{{ $v->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Nama Device</label>
                    <input name="device_name" class="form-control" placeholder="PC Operator Desa" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label">App Version</label>
                    <input name="app_version" class="form-control" value="{{ config('siapdesa.version') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Schema</label>
                    <input name="schema_version" class="form-control" value="{{ config('siapdesa.schema_version') }}">
                </div>
                <div class="col-12">
                    <button class="btn btn-primary">Daftarkan Device</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Device</th><th>Desa</th><th>Status</th><th>Versi</th><th>Last Seen</th><th>Last Sync</th><th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($devices as $device)
                    <tr>
                        <td><div class="fw-semibold">{{ $device->device_name }}</div><small class="text-muted">{{ $device->uuid }}</small></td>
                        <td>{{ $device->village?->name ?? $device->village_uuid }}</td>
                        <td><span class="badge text-bg-{{ $device->connection_status === 'ONLINE' ? 'success' : ($device->status === 'active' ? 'secondary' : 'danger') }}">{{ $device->connection_status }}</span></td>
                        <td>{{ $device->app_version ?: '-' }} / DB {{ $device->schema_version ?: '-' }}</td>
                        <td>{{ $device->last_seen_at?->format('d/m/Y H:i:s') ?: '-' }}</td>
                        <td>{{ $device->last_sync_at?->format('d/m/Y H:i:s') ?: '-' }}</td>
                        <td class="text-nowrap">
                            <form method="POST" action="{{ route('central.devices.toggle', $device) }}" class="d-inline">@csrf<button class="btn btn-sm btn-outline-warning">{{ $device->status === 'active' ? 'Blokir' : 'Aktifkan' }}</button></form>
                            @if($device->status !== 'retired')
                                <form method="POST" action="{{ route('central.devices.retire', $device) }}" class="d-inline">@csrf<button class="btn btn-sm btn-outline-danger">Pensiunkan</button></form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center py-4 text-muted">Belum ada device.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">{{ $devices->links() }}</div>
    </div>
</div>
@endsection
