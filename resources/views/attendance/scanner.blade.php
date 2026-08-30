@extends('layouts.app')

@section('title', 'Scan QR Code Absensi Kamera')

@push('styles')
<style>
    #reader {
        width: 100%;
        max-width: 480px;
        margin: auto;
        border-radius: 12px;
        overflow: hidden;
        border: 2px solid var(--siap-primary);
        background: #000;
        min-height: 280px;
    }
    #reader video {
        border-radius: 10px;
        object-fit: cover;
    }
    .scanner-box {
        background: #ffffff;
        border-radius: 1rem;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
        border: 1px solid var(--siap-border);
    }
</style>
@endpush

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1">Scanner Absensi Kamera Digital (QR Code)</h4>
        <p class="text-muted small mb-0">Arahkan Kartu QR Pegawai ke kamera webcam atau gunakan scanner barcode USB.</p>
    </div>
    <a href="{{ route('attendance.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="fa-solid fa-arrow-left me-1"></i> Kembali ke Rekap
    </a>
</div>

<div class="row g-4 justify-content-center">
    <!-- Camera Live Viewport Card -->
    <div class="col-lg-6">
        <div class="card p-3 p-md-4 text-center">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <span class="fw-bold fs-6"><i class="fa-solid fa-camera me-2 text-primary"></i>Kamera Pemindai Langsung</span>
                <span id="cameraStatusBadge" class="badge bg-secondary">Kamera Siap</span>
            </div>

            <!-- Video Viewport -->
            <div id="reader" class="mb-3"></div>

            <!-- Controls -->
            <div class="d-flex flex-wrap justify-content-center gap-2 mb-3">
                <select id="cameraSelect" class="form-select form-select-sm" style="max-width: 260px;">
                    <option value="">-- Mendeteksi Kamera... --</option>
                </select>
                <button id="btnStartCamera" class="btn btn-sm btn-primary px-3">
                    <i class="fa-solid fa-play me-1"></i> Mulai Kamera
                </button>
                <button id="btnStopCamera" class="btn btn-sm btn-outline-danger px-3" style="display: none;">
                    <i class="fa-solid fa-stop me-1"></i> Hentikan
                </button>
            </div>

            <!-- Result Box -->
            <div id="scanResult" style="display: none;" class="alert text-start mb-0"></div>
        </div>
    </div>

    <!-- Manual Barcode Input Fallback -->
    <div class="col-lg-5">
        <div class="card p-3 p-md-4">
            <h6 class="fw-bold text-dark mb-2"><i class="fa-solid fa-barcode me-2 text-success"></i>Scanner Barcode USB / Input Manual</h6>
            <p class="text-muted small">Jika perangkat komputer menggunakan scanner barcode fisik (USB/Wireless), arahkan kursor ke kolom di bawah ini:</p>

            <form id="scanForm" class="mb-3">
                <label class="form-label small fw-semibold">Kode Token QR Pegawai</label>
                <div class="input-group">
                    <input type="text" id="qrInput" class="form-control fw-bold" placeholder="Contoh: QR-EMP-..." autofocus autocomplete="off">
                    <button class="btn btn-success" type="submit"><i class="fa-solid fa-check me-1"></i> Proses</button>
                </div>
            </form>

            <div class="p-3 bg-light rounded border small text-muted">
                <div class="fw-bold text-dark mb-1"><i class="fa-solid fa-circle-info me-1 text-primary"></i> Petunjuk Penggunaan:</div>
                <ul class="mb-0 ps-3">
                    <li>Pastikan izin akses kamera (Webcam) diizinkan di peramban browser.</li>
                    <li>Dekatkan kartu QR pegawai dengan jarak 15 - 30 cm dari lensa kamera.</li>
                    <li>Sistem akan berbunyi *bip* secara otomatis saat kode berhasil dibaca.</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<!-- HTML5 QR Code Scanner Library -->
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
let html5QrCode = null;
let isScanning = false;
let isProcessing = false;

// Audio Beep Generator using Web Audio API (Zero external file dependencies)
function playBeep(success = true) {
    try {
        const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
        const osc = audioCtx.createOscillator();
        const gain = audioCtx.createGain();
        osc.connect(gain);
        gain.connect(audioCtx.destination);

        if (success) {
            osc.frequency.setValueAtTime(880, audioCtx.currentTime); // High pitch for success
            gain.gain.setValueAtTime(0.3, audioCtx.currentTime);
            osc.start();
            osc.stop(audioCtx.currentTime + 0.15);
        } else {
            osc.frequency.setValueAtTime(300, audioCtx.currentTime); // Low pitch for error
            gain.gain.setValueAtTime(0.4, audioCtx.currentTime);
            osc.start();
            osc.stop(audioCtx.currentTime + 0.3);
        }
    } catch (e) { }
}

function displayResult(data) {
    const resBox = document.getElementById('scanResult');
    resBox.style.display = 'block';
    if (data.success) {
        playBeep(true);
        resBox.className = 'alert alert-success text-start shadow-sm';
        resBox.innerHTML = `
            <div class="d-flex align-items-center">
                <i class="fa-solid fa-circle-check fs-2 me-3 text-success"></i>
                <div>
                    <h6 class="fw-bold mb-1">${data.message}</h6>
                    <small>Pegawai: <strong>${data.employee_name}</strong> (${data.position})</small><br>
                    <small>Waktu: <strong>${data.time}</strong> • Status: <span class="badge bg-success">${data.status}</span></small>
                </div>
            </div>
        `;
    } else {
        playBeep(false);
        resBox.className = 'alert alert-danger text-start shadow-sm';
        resBox.innerHTML = `<i class="fa-solid fa-circle-xmark me-2"></i> ${data.message}`;
    }
}

function processToken(token) {
    if (isProcessing || !token) return;
    isProcessing = true;

    fetch("{{ route('attendance.scan.submit') }}", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ qr_token: token })
    })
    .then(res => res.json())
    .then(data => {
        displayResult(data);
        document.getElementById('qrInput').value = '';
        setTimeout(() => { isProcessing = false; }, 2000); // 2 seconds debounce
    })
    .catch(err => {
        displayResult({ success: false, message: 'Gagal menghubungi server absensi.' });
        setTimeout(() => { isProcessing = false; }, 2000);
    });
}

// Camera Scanner Implementation
function initCameras() {
    Html5Qrcode.getCameras().then(devices => {
        const select = document.getElementById('cameraSelect');
        select.innerHTML = '';
        if (devices && devices.length) {
            devices.forEach((cam, idx) => {
                const opt = document.createElement('option');
                opt.value = cam.id;
                opt.text = cam.label || `Kamera ${idx + 1}`;
                select.appendChild(opt);
            });
            // Auto start camera if available
            startCamera(devices[0].id);
        } else {
            select.innerHTML = '<option value="">Tidak ada kamera terdeteksi</option>';
            document.getElementById('cameraStatusBadge').className = 'badge bg-warning text-dark';
            document.getElementById('cameraStatusBadge').innerText = 'Kamera Tidak Ditemukan';
        }
    }).catch(err => {
        console.log("Camera access error:", err);
        document.getElementById('cameraStatusBadge').className = 'badge bg-danger';
        document.getElementById('cameraStatusBadge').innerText = 'Izin Kamera Ditolak';
    });
}

function startCamera(cameraId) {
    if (!cameraId) {
        cameraId = document.getElementById('cameraSelect').value;
    }
    if (!cameraId) return;

    if (!html5QrCode) {
        html5QrCode = new Html5Qrcode("reader");
    }

    const config = {
        fps: 15,
        qrbox: { width: 250, height: 250 },
        aspectRatio: 1.333334
    };

    html5QrCode.start(
        cameraId,
        config,
        (decodedText, decodedResult) => {
            processToken(decodedText);
        },
        (errorMessage) => { }
    ).then(() => {
        isScanning = true;
        document.getElementById('btnStartCamera').style.display = 'none';
        document.getElementById('btnStopCamera').style.display = 'inline-block';
        document.getElementById('cameraStatusBadge').className = 'badge bg-success';
        document.getElementById('cameraStatusBadge').innerText = 'Kamera Aktif & Memindai';
    }).catch(err => {
        document.getElementById('cameraStatusBadge').className = 'badge bg-danger';
        document.getElementById('cameraStatusBadge').innerText = 'Gagal Membuka Kamera';
    });
}

function stopCamera() {
    if (html5QrCode && isScanning) {
        html5QrCode.stop().then(() => {
            isScanning = false;
            document.getElementById('btnStartCamera').style.display = 'inline-block';
            document.getElementById('btnStopCamera').style.display = 'none';
            document.getElementById('cameraStatusBadge').className = 'badge bg-secondary';
            document.getElementById('cameraStatusBadge').innerText = 'Kamera Nonaktif';
        }).catch(err => { });
    }
}

document.getElementById('btnStartCamera').addEventListener('click', () => {
    startCamera();
});

document.getElementById('btnStopCamera').addEventListener('click', () => {
    stopCamera();
});

document.getElementById('cameraSelect').addEventListener('change', function() {
    if (isScanning) {
        stopCamera();
        setTimeout(() => startCamera(this.value), 400);
    }
});

// Manual form submission
document.getElementById('scanForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const token = document.getElementById('qrInput').value.trim();
    if (token) {
        processToken(token);
    }
});

// Initialize on page load
document.addEventListener('DOMContentLoaded', () => {
    initCameras();
});
</script>
@endpush
