@extends('layouts.app')

@section('title', 'Pembaruan & Patch Fitur')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1">Update & Patch Center (GitHub & Cloud OTA)</h4>
        <p class="text-muted small mb-0">Pembaruan fitur program, patch perbaikan, dan migrasi skema database tanpa install ulang.</p>
    </div>
    <div class="d-flex gap-2">
        <button class="btn btn-outline-primary btn-sm" id="btnCheckOnline">
            <i class="fa-solid fa-cloud-arrow-down me-1"></i> Periksa Update Online (GitHub)
        </button>
        <a href="{{ route('system.settings') }}" class="btn btn-outline-secondary btn-sm">
            <i class="fa-solid fa-arrow-left me-1"></i> Kembali ke Pengaturan
        </a>
    </div>
</div>

<div id="updateAlertBox" class="alert mb-4" style="display: none;"></div>

<div class="row g-4">
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-header"><i class="fa-solid fa-upload me-2 text-primary"></i>Upload & Terapkan Paket Patch (.zip / .pkg)</div>
            <form action="{{ route('system.updates.patch') }}" method="POST" enctype="multipart/form-data" class="card-body p-4" onsubmit="return confirm('Sistem akan melakukan snapshot backup database secara otomatis sebelum menerapkan patch. Lanjutkan?')">
                @csrf
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Pilih File Paket Patch (*.zip / *.pkg) *</label>
                    <input type="file" name="patch_file" class="form-control" accept=".zip,.pkg" required>
                    <small class="text-muted">Paket patch resmi dari Central Cloud atau GitHub Release.</small>
                </div>

                <div class="p-3 bg-light rounded border mb-4 small">
                    <div class="fw-bold text-primary mb-1"><i class="fa-solid fa-shield-halved me-1"></i> Safe Update Guarantee:</div>
                    <ul class="mb-0 ps-3 text-muted">
                        <li>Folder database & dokumen <code>C:\ProgramData\SIAP Desa</code> tetap aman.</li>
                        <li>Snapshot backup database otomatis dibuat sebelum patch diekstrak.</li>
                        <li>Perintah <code>artisan migrate --force</code> berjalan otomatis untuk skema baru.</li>
                    </ul>
                </div>

                <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold">
                    <i class="fa-solid fa-bolt me-1"></i> Terapkan Pembaruan Sekarang
                </button>
            </form>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-header"><i class="fa-solid fa-clock-rotate-left me-2 text-primary"></i>Riwayat Pembaruan (Update Logs)</div>
            <div class="table-responsive" style="max-height: 350px; overflow-y: auto;">
                <table class="table table-hover mb-0 small">
                    <thead class="table-light">
                        <tr>
                            <th>Waktu</th>
                            <th>Dari Versi</th>
                            <th>Ke Versi</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($updateLogs as $log)
                        <tr>
                            <td>{{ $log->created_at->format('d/m/Y H:i') }}</td>
                            <td>v{{ $log->app_version_from }}</td>
                            <td><strong class="text-success">v{{ $log->app_version_to }}</strong></td>
                            <td><span class="badge bg-success">{{ $log->status }}</span></td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted py-4">Versi awal (v{{ $currentVersion->app_version ?? '1.0.0' }} baseline).</td>
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
document.getElementById('btnCheckOnline').addEventListener('click', function() {
    const alertBox = document.getElementById('updateAlertBox');
    alertBox.style.display = 'block';
    alertBox.className = 'alert alert-info py-2 small';
    alertBox.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-2"></i> Memeriksa rilis terbaru di Central Server & GitHub...';

    fetch("{{ route('system.updates.check') }}")
        .then(res => res.json())
        .then(data => {
            if (data.update_available) {
                alertBox.className = 'alert alert-warning py-3';
                alertBox.innerHTML = `
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="fw-bold mb-1 text-dark"><i class="fa-solid fa-bell text-warning me-2"></i>Versi Baru Tersedia: v${data.latest_version}</h6>
                            <p class="mb-0 small text-muted">${data.title} • Dirilis: ${data.release_date}</p>
                        </div>
                        <a href="${data.download_url}" target="_blank" class="btn btn-sm btn-primary">
                            <i class="fa-solid fa-download me-1"></i> Unduh di GitHub
                        </a>
                    </div>
                `;
            } else {
                alertBox.className = 'alert alert-success py-2 small';
                alertBox.innerHTML = `<i class="fa-solid fa-circle-check me-2"></i> Aplikasi Anda sudah menggunakan versi paling mutakhir (v${data.current_version}).`;
            }
        })
        .catch(err => {
            alertBox.className = 'alert alert-secondary py-2 small';
            alertBox.innerHTML = '<i class="fa-solid fa-circle-info me-2"></i> Belum dapat menghubungi server rilis online.';
        });
});
</script>
@endpush
