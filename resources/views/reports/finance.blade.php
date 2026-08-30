<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Realisasi Kas & Keuangan APBDes</title>
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
    <h4 style="margin:0;">BUKU KAS UMUM REALISASI KEUANGAN</h4>
    <small>Dicetak pada: {{ date('d/m/Y H:i') }}</small>
</div>
<table>
    <thead>
        <tr>
            <th>No. Bukti</th>
            <th>Tanggal</th>
            <th>Akun</th>
            <th>Uraian Transaksi</th>
            <th style="text-align: right;">Penerimaan (Rp)</th>
            <th style="text-align: right;">Pengeluaran (Rp)</th>
        </tr>
    </thead>
    <tbody>
        @foreach($transactions as $t)
        <tr>
            <td>{{ $t->transaction_number }}</td>
            <td>{{ $t->transaction_date->format('d/m/Y') }}</td>
            <td>{{ $t->account->name ?? '-' }}</td>
            <td>{{ $t->description }}</td>
            <td style="text-align: right;">{{ $t->type == 'PENERIMAAN' ? number_format($t->amount, 0, ',', '.') : '-' }}</td>
            <td style="text-align: right;">{{ $t->type == 'PENGELUARAN' ? number_format($t->amount, 0, ',', '.') : '-' }}</td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr style="font-weight: bold; background: #f9f9f9;">
            <td colspan="4" style="text-align: right;">TOTAL:</td>
            <td style="text-align: right; color: green;">Rp {{ number_format($income, 0, ',', '.') }}</td>
            <td style="text-align: right; color: red;">Rp {{ number_format($expense, 0, ',', '.') }}</td>
        </tr>
        <tr style="font-weight: bold; background: #eef2ff;">
            <td colspan="4" style="text-align: right;">SALDO KAS BERJALAN:</td>
            <td colspan="2" style="text-align: center; color: #1e3a8a;">Rp {{ number_format($income - $expense, 0, ',', '.') }}</td>
        </tr>
    </tfoot>
</table>
</body>
</html>
