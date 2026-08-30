@extends('layouts.central')

@section('title', 'Pengaturan Server Central & Supabase')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1">Pengaturan Server Central & Integrasi Supabase Cloud</h4>
        <p class="text-muted small mb-0">Konfigurasi pusat untuk kredensial Supabase PostgreSQL Cloud, interval sinkronisasi, dan distribusi otomatis ke klien desa.</p>
    </div>
    <button type="button" class="btn btn-outline-success btn-sm" id="btnTestSupabase">
        <i class="fa-solid fa-cloud-bolt me-1"></i> Tes Ping Supabase Cloud
    </button>
</div>

<div id="supabasePingAlert" class="alert mb-4" style="display: none;"></div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="fa-solid fa-database me-2 text-primary"></i>Koneksi Supabase Cloud PostgreSQL & REST API</span>
                <span class="badge bg-success"><i class="fa-solid fa-check me-1"></i> Supabase Key Active</span>
            </div>
            <form action="{{ route('central.settings.update') }}" method="POST" class="card-body p-4">
                @csrf
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Supabase REST API Endpoint URL *</label>
                    <input type="url" name="supabase_url" id="inputSupabaseUrl" class="form-control" value="{{ $supabaseUrl }}" required placeholder="https://xxx.supabase.co">
                    <small class="text-muted">URL backend Supabase Central Cloud PostgreSQL.</small>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-semibold">Supabase API Publishable Key / Central Key *</label>
                    <div class="input-group">
                        <input type="text" name="supabase_key" id="inputSupabaseKey" class="form-control font-monospace" value="{{ $supabaseKey }}" required>
                        <button class="btn btn-outline-secondary" type="button" onclick="navigator.clipboard.writeText('{{ $supabaseKey }}'); alert('Key disalin!');"><i class="fa-solid fa-copy"></i></button>
                    </div>
                    <small class="text-muted">Kunci ini otomatis dikirim ke klien desa saat mereka memasukkan Kode Lisensi (tanpa input manual).</small>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Interval Sinkronisasi Otomatis (Detik) *</label>
                        <input type="number" name="sync_interval_seconds" class="form-control" value="{{ $syncInterval }}" min="5" max="3600" required>
                        <small class="text-muted">Frekuensi client melakukan pull delta perubahan data.</small>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">GitHub Repository Identifier *</label>
                        <input type="text" name="github_repo" class="form-control" value="{{ $githubRepo }}" required placeholder="Owner/Repo">
                        <small class="text-muted">Digunakan oleh klien untuk auto-check rilis patch OTA.</small>
                    </div>
                </div>

                <div class="form-check form-switch mb-4">
                    <input class="form-check-input" type="checkbox" name="realtime_sync_enabled" value="1" id="realtimeSwitch" {{ $realtimeSync ? 'checked' : '' }}>
                    <label class="form-check-label small fw-semibold" for="realtimeSwitch">
                        Aktifkan Real-Time Instant Push Sync (Klien langsung mengirim mutasi saat data dibuat)
                    </label>
                </div>

                <div class="p-3 bg-light rounded border mb-4 small">
                    <div class="fw-bold text-primary mb-1"><i class="fa-solid fa-lock me-1"></i> Arsitektur Cloud Terintegrasi Penuh:</div>
                    <p class="mb-0 text-muted">
                        Central Server bertindak sebagai jembatan *Security & License Ingestion Layer* antara klien desa dan Supabase Cloud. Seluruh mutasi delta klien di-stream secara terenkripsi ke database pusat.
                    </p>
                </div>

                <button type="submit" class="btn btn-primary px-4 py-2">
                    <i class="fa-solid fa-save me-1"></i> Simpan & Terapkan Konfigurasi
                </button>
            </form>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card bg-indigo text-white mb-4" style="background-color: #1e1b4b;">
            <div class="card-body p-4">
                <h6 class="fw-bold text-warning mb-2"><i class="fa-solid fa-bolt me-2"></i>Status Integrasi Supabase</h6>
                <div class="d-flex justify-content-between py-2 border-bottom border-secondary border-opacity-25 small">
                    <span>Engine:</span>
                    <span class="badge bg-success">ONLINE</span>
                </div>
                <div class="d-flex justify-content-between py-2 border-bottom border-secondary border-opacity-25 small">
                    <span>Target DB:</span>
                    <span>Supabase PostgreSQL</span>
                </div>
                <div class="d-flex justify-content-between py-2 border-bottom border-secondary border-opacity-25 small">
                    <span>Protocol:</span>
                    <span>HTTPS REST + PostgREST</span>
                </div>
                <div class="d-flex justify-content-between py-2 small">
                    <span>Auth:</span>
                    <span class="text-warning font-monospace">Publishable Key</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('btnTestSupabase').addEventListener('click', function() {
    const alertBox = document.getElementById('supabasePingAlert');
    alertBox.style.display = 'block';
    alertBox.className = 'alert alert-info py-2 small';
    alertBox.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-2"></i> Melakukan ping ke Supabase Cloud REST API...';

    fetch("{{ route('central.settings.test-supabase') }}", {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json'
        }
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            alertBox.className = 'alert alert-success py-2 small';
            alertBox.innerHTML = '<i class="fa-solid fa-circle-check me-2"></i> <strong>Terhubung ke Supabase Cloud!</strong> ' + data.message;
        } else {
            alertBox.className = 'alert alert-warning py-2 small';
            alertBox.innerHTML = '<i class="fa-solid fa-triangle-exclamation me-2"></i> ' + data.message;
        }
    })
    .catch(err => {
        alertBox.className = 'alert alert-danger py-2 small';
        alertBox.innerHTML = '<i class="fa-solid fa-circle-xmark me-2"></i> Gagal menghubungi endpoint Supabase: ' + err;
    });
});
</script>
@endpush
