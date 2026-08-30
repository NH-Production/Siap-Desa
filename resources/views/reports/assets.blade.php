<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Buku Inventaris Aset Desa</title>
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
    <h4 style="margin:0;">BUKU INVENTARIS BARANG MILIK DESA (KIB)</h4>
    <small>Dicetak pada: {{ date('d/m/Y H:i') }}</small>
</div>
<table>
    <thead>
        <tr>
            <th>Kode Aset</th>
            <th>Nama Aset / Barang</th>
            <th>Kategori</th>
            <th>Tahun Perolehan</th>
            <th>Kondisi</th>
            <th>Lokasi</th>
            <th style="text-align: right;">Nilai Aset (Rp)</th>
        </tr>
    </thead>
    <tbody>
        @foreach($assets as $a)
        <tr>
            <td>{{ $a->asset_code }}</td>
            <td><strong>{{ $a->name }}</strong></td>
            <td>{{ $a->category->name ?? '-' }}</td>
            <td>{{ $a->acquisition_date ? $a->acquisition_date->format('Y') : '-' }}</td>
            <td>{{ $a->condition }}</td>
            <td>{{ $a->location ?? '-' }}</td>
            <td style="text-align: right;">Rp {{ number_format($a->acquisition_cost, 0, ',', '.') }}</td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr style="font-weight: bold; background: #f2f2f2;">
            <td colspan="6" style="text-align: right;">TOTAL NILAI KEKAYAAN ASET DESA:</td>
            <td style="text-align: right; color: #1e3a8a;">Rp {{ number_format($totalValue, 0, ',', '.') }}</td>
        </tr>
    </tfoot>
</table>
</body>
</html>
