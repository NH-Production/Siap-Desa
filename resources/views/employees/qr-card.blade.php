<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kartu Absensi Pegawai - {{ $employee->name }}</title>
    <style>
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background: #f1f5f9;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }
        .card-container {
            width: 320px;
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            overflow: hidden;
            border: 2px solid #1e3a8a;
            text-align: center;
        }
        .card-header {
            background: #1e3a8a;
            color: #ffffff;
            padding: 1rem 0.5rem;
        }
        .card-header h3 {
            margin: 0;
            font-size: 1.1rem;
            text-transform: uppercase;
        }
        .card-body {
            padding: 1.5rem;
        }
        .qr-box {
            margin: 1rem 0;
        }
        .qr-box img {
            width: 180px;
            height: 180px;
        }
        .emp-name {
            font-size: 1.15rem;
            font-weight: bold;
            color: #0f172a;
            margin: 0.5rem 0 0.2rem;
        }
        .emp-pos {
            font-size: 0.9rem;
            color: #2563eb;
            font-weight: 600;
        }
        .emp-nip {
            font-size: 0.8rem;
            color: #64748b;
        }
        .card-footer {
            background: #f8fafc;
            padding: 0.75rem;
            font-size: 0.75rem;
            color: #64748b;
            border-top: 1px dashed #cbd5e1;
        }
        @media print {
            body { background: white; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>

<div>
    <div class="no-print" style="margin-bottom: 1rem; text-align: center;">
        <button onclick="window.print()" style="padding: 0.5rem 1.5rem; background: #1e3a8a; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: bold;">
            Cetak Kartu QR
        </button>
    </div>

    <div class="card-container">
        <div class="card-header">
            <h3>{{ $village->name ?? 'PEMERINTAH DESA' }}</h3>
            <small>KARTU ABSENSI DIGITAL PEGAWAI</small>
        </div>
        <div class="card-body">
            <div class="qr-box">
                <img src="{{ $qrDataUri }}" alt="QR Code">
            </div>
            <div class="emp-name">{{ $employee->name }}</div>
            <div class="emp-pos">{{ $employee->position }}</div>
            <div class="emp-nip">NIP/ID: {{ $employee->nip ?? $employee->qr_token }}</div>
        </div>
        <div class="card-footer">
            Scan kartu ini pada Scanner Absensi SIAP Desa
        </div>
    </div>
</div>

</body>
</html>
