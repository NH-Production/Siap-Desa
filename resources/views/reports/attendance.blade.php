<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Absensi Kehadiran Aparat Desa</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 10pt; line-height: 1.4; padding: 1.5cm; }
        .header { text-align: center; border-bottom: 2px solid #000; padding-bottom: 8px; margin-bottom: 15px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #333; padding: 5px; text-align: left; }
        th { background: #f2f2f2; }
        @media print { .no-print { display: none; } body { padding: 0; } }
    </style>
</head>
<body>
<div class="no-print" style="margin-bottom: 15px; text-align: right;">
    <button onclick="window.print()" style="padding: 6px 15px; background: #1e3a8a; color: white; border: none; border-radius: 4px; cursor: pointer;">Cetak Laporan</button>
</div>
<div class="header">
    <h3 style="margin:0;">PEMERINTAH DESA {{ strtoupper($village->name ?? 'SUKAMAJU SEJAHTERA') }}</h3>
    <h4 style="margin:0;">REKAPITULASI DAFTAR HADIR APARAT DESA (BULAN {{ $month }})</h4>
    <small>Dicetak pada: {{ date('d/m/Y H:i') }}</small>
</div>
<table>
    <thead>
        <tr>
            <th>Tanggal</th>
            <th>Nama Pegawai</th>
            <th>Jabatan</th>
            <th>Jam Masuk</th>
            <th>Jam Pulang</th>
            <th>Status</th>
            <th>Keterangan</th>
        </tr>
    </thead>
    <tbody>
        @foreach($attendances as $att)
        <tr>
            <td>{{ $att->date->format('d/m/Y') }}</td>
            <td><strong>{{ $att->employee->name ?? '-' }}</strong></td>
            <td>{{ $att->employee->position ?? '-' }}</td>
            <td>{{ $att->time_in ?? '-' }}</td>
            <td>{{ $att->time_out ?? '-' }}</td>
            <td>{{ $att->status }}</td>
            <td>{{ $att->notes ?? '-' }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
</body>
</html>
