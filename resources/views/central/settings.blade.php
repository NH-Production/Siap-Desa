@extends('layouts.central')

@section('title', 'Pengaturan Server Central & Sinkronisasi')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1">Pengaturan Server Central & Kebijakan Sinkronisasi</h4>
        <p class="text-muted small mb-0">Konfigurasi pusat untuk kredensial Supabase Cloud, interval sinkronisasi, dan distribusi otomatis ke klien desa.</p>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header"><i class="fa-solid fa-sliders me-2 text-primary"></i>Konfigurasi Cloud Supabase & REST API</div>
            <form action="{{ route('central.settings.update') }}" method="POST" class="card-body p-4">
                @csrf
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Supabase REST API Endpoint URL *</label>
                    <input type="url" name="supabase_url" class="form-control" value="{{ $supabaseUrl }}" required placeholder="https://xxx.supabase.co">
                    <small class="text-muted">URL backend Supabase Central Cloud PostgreSQL.</small>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-semibold">Supabase API Publishable Key / Central Key *</label>
                    <div class="input-group">
                        <input type="text" name="supabase_key" class="form-control font-monospace" value="{{ $supabaseKey }}" required>
                        <button class="btn btn-outline-secondary" type="button" onclick="navigator.clipboard.writeText('{{ $supabaseKey }}'); alert('Key disalin!');"><i class="fa-solid fa-copy"></i></button>
                    </div>
                    <small class="text-muted">Kunci ini akan otomatis didistribusikan ke klien desa saat mereka memasukkan Kode Lisensi.</small>
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
                    <div class="fw-bold text-primary mb-1"><i class="fa-solid fa-lock me-1"></i> Keamanan Client ID & Lisensi Terpusat:</div>
                    <p class="mb-0 text-muted">
                        Operator desa <strong>tidak perlu lagi menginput API Key atau URL</strong> secara manual. Cukup masukkan Kode Lisensi Desa, dan sistem klien akan mengunci Client ID unik dan mengunduh seluruh konfigurasi ini secara otomatis dan terenkripsi.
                    </p>
                </div>

                <button type="submit" class="btn btn-primary px-4 py-2">
                    <i class="fa-solid fa-save me-1"></i> Simpan & Terapkan Konfigurasi ke Seluruh Klien
                </button>
            </form>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card bg-indigo text-white mb-4" style="background-color: #1e1b4b;">
            <div class="card-body p-4">
                <h6 class="fw-bold text-warning mb-2"><i class="fa-solid fa-bolt me-2"></i>Status Server Real-Time</h6>
                <div class="d-flex justify-content-between py-2 border-bottom border-secondary border-opacity-25 small">
                    <span>Engine:</span>
                    <span class="badge bg-success">ONLINE</span>
                </div>
                <div class="d-flex justify-content-between py-2 border-bottom border-secondary border-opacity-25 small">
                    <span>Database Sync:</span>
                    <span>Active Supabase REST</span>
                </div>
                <div class="d-flex justify-content-between py-2 border-bottom border-secondary border-opacity-25 small">
                    <span>Auto Distribute:</span>
                    <span>Aktif via Token</span>
                </div>
                <div class="d-flex justify-content-between py-2 small">
                    <span>Versi Master:</span>
                    <span class="text-warning fw-bold">v1.0.0 Enterprise</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
