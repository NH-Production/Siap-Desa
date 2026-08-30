@extends('layouts.app')

@section('title', 'Koneksi SIAP CLOUD & Lisensi Desa')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1">Aktivasi Lisensi Desa & Koneksi SIAP CLOUD</h4>
        <p class="text-muted small mb-0">Hubungkan komputer desa ke Server SIAP CLOUD menggunakan Kode Lisensi untuk mengunci Client ID unik.</p>
    </div>
    <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary btn-sm">
        <i class="fa-solid fa-arrow-left me-1"></i> Kembali ke Dashboard
    </a>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header"><i class="fa-solid fa-cloud-bolt me-2 text-primary"></i>Aktivasi Lisensi & Penguncian Client ID</div>
            <div class="card-body p-4">
                <form id="formActivateLicense">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Kode Lisensi SAAS Desa *</label>
                        <div class="input-group">
                            <input type="text" name="license_key" id="licenseKeyInput" class="form-control font-monospace fw-bold" value="{{ $settings['saas_license_key'] }}" required placeholder="Contoh: SIAP-SAAS-3203-1620-02-2026">
                            <button class="btn btn-primary" type="button" id="btnActivateLicense">
                                <i class="fa-solid fa-key me-1"></i> Kunci Client ID
                            </button>
                        </div>
                        <small class="text-muted">Kode lisensi resmi yang diterbitkan oleh Administrator SIAP CLOUD.</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Central Server API Endpoint URL *</label>
                        <input type="url" name="central_api_url" id="centralUrlInput" class="form-control" value="{{ $settings['central_api_url'] }}" required placeholder="http://127.0.0.1:8090/api/v1">
                        <small class="text-muted">Alamat server cloud SIAP Desa (Lokal: Port 8090 atau Domain Publik: https://api.siapdesa.id/api/v1).</small>
                    </div>

                    <div class="p-3 bg-light rounded border mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="small fw-semibold text-muted">Status Terkunci Client ID:</span>
                            <span class="badge bg-indigo text-white" id="badgeClientId" style="background-color: #4f46e5;">
                                <i class="fa-solid fa-lock me-1"></i> {{ $settings['locked_client_id'] }}
                            </span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="small fw-semibold text-muted">Status Lisensi:</span>
                            <span class="badge bg-success" id="badgeLicenseStatus">{{ $settings['saas_status'] }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="small fw-semibold text-muted">Sinkronisasi Realtime:</span>
                            <span class="badge bg-info text-dark"><i class="fa-solid fa-circle-check text-success me-1"></i> Terhubung Otomatis</span>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center pt-2">
                        <button type="button" class="btn btn-outline-success" id="btnTestConnection">
                            <i class="fa-solid fa-satellite-dish me-1"></i> Tes Koneksi Real-Time
                        </button>
                        <div id="connResult" class="small fw-semibold"></div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card bg-indigo text-white mb-4" style="background-color: #0f172a;">
            <div class="card-body p-4">
                <h6 class="fw-bold text-warning mb-2"><i class="fa-solid fa-shield-halved me-2"></i>Keamanan Bebas Input API</h6>
                <p class="small text-light text-opacity-75 mb-3">
                    Seluruh kunci rahasia Supabase, endpoint database, dan kebijakan sinkronisasi telah diatur terpusat pada <strong>SIAP CLOUD Panel</strong>.
                </p>
                <div class="p-3 bg-white bg-opacity-10 rounded small">
                    <div class="fw-bold text-warning mb-1">Keunggulan Sistem:</div>
                    <ul class="mb-0 ps-3 text-light text-opacity-75">
                        <li>Tidak perlu input API Key manual.</li>
                        <li>Client ID terkunci otomatis per desa.</li>
                        <li>Perubahan server langsung tersinkronisasi.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function performHandshake() {
    const licenseKey = document.getElementById('licenseKeyInput').value;
    const centralUrl = document.getElementById('centralUrlInput').value;
    const resultDiv = document.getElementById('connResult');

    resultDiv.innerHTML = '<span class="text-primary"><i class="fa-solid fa-spinner fa-spin me-1"></i> Menghubungi SIAP CLOUD & mengunci Client ID...</span>';

    fetch("{{ route('system.settings.test-central') }}", {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ license_key: licenseKey, central_api_url: centralUrl })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            resultDiv.innerHTML = '<span class="text-success"><i class="fa-solid fa-circle-check me-1"></i> ' + data.message + '</span>';
            document.getElementById('badgeClientId').innerHTML = '<i class="fa-solid fa-lock me-1"></i> ' + data.client_id;
        } else {
            resultDiv.innerHTML = '<span class="text-danger"><i class="fa-solid fa-circle-xmark me-1"></i> ' + data.message + '</span>';
        }
    })
    .catch(err => {
        resultDiv.innerHTML = '<span class="text-danger"><i class="fa-solid fa-circle-xmark me-1"></i> Gagal terhubung: ' + err + '</span>';
    });
}

document.getElementById('btnActivateLicense').addEventListener('click', performHandshake);
document.getElementById('btnTestConnection').addEventListener('click', performHandshake);
</script>
@endpush
