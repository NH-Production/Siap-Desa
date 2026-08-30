@extends('layouts.app')

@section('title', 'Pengaturan Server & Cloud')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1">Pengaturan Server Central & Sinkronisasi</h4>
        <p class="text-muted small mb-0">Konfigurasi endpoint Central Cloud (Supabase / Central API) dan database lokal.</p>
    </div>
    <a href="{{ route('system.updates') }}" class="btn btn-outline-primary btn-sm">
        <i class="fa-solid fa-cloud-arrow-down me-1"></i> Update & Patch Center
    </a>
</div>

<div class="row g-4">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header"><i class="fa-solid fa-cloud me-2 text-primary"></i>Koneksi Central Cloud Server (Supabase / Central API)</div>
            <form action="{{ route('system.settings.update') }}" method="POST" class="card-body p-4">
                @csrf
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Central API URL (HTTPS) *</label>
                    <div class="input-group">
                        <input type="url" name="central_api_url" id="apiUrl" class="form-control" value="{{ $settings['central_api_url'] }}" required placeholder="https://api.siapdesa.id/api/v1">
                        <button class="btn btn-outline-secondary" type="button" id="btnTestConn">
                            <i class="fa-solid fa-plug me-1"></i> Tes Koneksi
                        </button>
                    </div>
                    <small class="text-muted">Endpoint REST API Supabase / PostgreSQL Sync Layer untuk pertukaran data multi-perangkat.</small>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-semibold">Central API Secret / Bearer Token</label>
                    <input type="password" name="central_api_key" id="apiKey" class="form-control" value="{{ $settings['central_api_key'] }}" placeholder="Masukkan API Key / JWT Secret jika diaktifkan">
                </div>

                <div id="testResult" class="alert mb-3" style="display: none;"></div>

                <hr class="my-4">

                <h6 class="fw-bold text-primary mb-3"><i class="fa-solid fa-sliders me-2"></i>Otomatisasi & Operasional</h6>
                
                <div class="mb-3 form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="sync_enabled" value="1" id="syncEnabled" {{ $settings['sync_enabled'] ? 'checked' : '' }}>
                    <label class="form-check-label small fw-semibold" for="syncEnabled">Aktifkan Background Delta Synchronization</label>
                    <div class="text-muted" style="font-size: 0.75rem;">Mencatat setiap mutasi data lokal ke tabel Sync Queue.</div>
                </div>

                <div class="mb-3 form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="auto_backup" value="1" id="autoBackup" {{ $settings['auto_backup'] ? 'checked' : '' }}>
                    <label class="form-check-label small fw-semibold" for="autoBackup">Otomatis Buat Snapshot Backup Sebelum Update Patch</label>
                    <div class="text-muted" style="font-size: 0.75rem;">Mencegah kehilangan data jika terjadi kegagalan skema atau file.</div>
                </div>

                <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="fa-solid fa-save me-1"></i> Simpan Pengaturan Server
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card mb-3">
            <div class="card-header"><i class="fa-solid fa-info-circle me-2 text-primary"></i>Status Server Lokal</div>
            <div class="card-body small">
                <div class="mb-2">
                    <span class="text-muted d-block">Aplikasi:</span>
                    <strong>SIAP Desa v{{ $version->app_version ?? '1.0.0' }}</strong>
                </div>
                <div class="mb-2">
                    <span class="text-muted d-block">Database Lokal:</span>
                    <code>{{ config('database.default') }}</code> (Offline-First)
                </div>
                <div class="mb-2">
                    <span class="text-muted d-block">ProgramData Folder:</span>
                    <code>C:\ProgramData\SIAP Desa\</code>
                </div>
                <div>
                    <span class="text-muted d-block">Device Code:</span>
                    <strong>{{ $device->device_code ?? 'PC-ADMIN' }}</strong>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('btnTestConn').addEventListener('click', function() {
    const url = document.getElementById('apiUrl').value.trim();
    const key = document.getElementById('apiKey').value.trim();
    const resultBox = document.getElementById('testResult');
    
    resultBox.style.display = 'block';
    resultBox.className = 'alert alert-info py-2 small';
    resultBox.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-2"></i> Menghubungi central server...';

    fetch("{{ route('system.settings.test-central') }}", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": document.querySelector('meta[name=\"csrf-token\"]').content
        },
        body: JSON.stringify({ url: url, key: key })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            resultBox.className = 'alert alert-success py-2 small';
            resultBox.innerHTML = `<i class="fa-solid fa-circle-check me-2"></i> ${data.message}`;
        } else {
            resultBox.className = 'alert alert-danger py-2 small';
            resultBox.innerHTML = `<i class="fa-solid fa-circle-xmark me-2"></i> ${data.message}`;
        }
    })
    .catch(err => {
        resultBox.className = 'alert alert-danger py-2 small';
        resultBox.innerHTML = '<i class="fa-solid fa-circle-xmark me-2"></i> Gagal menghubungi endpoint server.';
    });
});
</script>
@endpush
