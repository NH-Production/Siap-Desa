<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Surat - {{ $letter->letter_number ?? $letter->draft_number }}</title>
    <style>
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12pt;
            line-height: 1.5;
            color: #000;
            margin: 0;
            padding: 2cm 2.5cm;
            background: #fff;
        }
        .header {
            text-align: center;
            border-bottom: 3px double #000;
            padding-bottom: 8px;
            margin-bottom: 20px;
        }
        .header h3 { margin: 0; font-size: 14pt; text-transform: uppercase; font-weight: bold; }
        .header h4 { margin: 0; font-size: 12pt; text-transform: uppercase; font-weight: bold; }
        .header p { margin: 2px 0 0; font-size: 10pt; font-style: italic; }
        
        .title-box {
            text-align: center;
            margin-bottom: 25px;
        }
        .title-box h3 {
            margin: 0;
            font-size: 13pt;
            text-transform: uppercase;
            text-decoration: underline;
            font-weight: bold;
        }
        .title-box p { margin: 2px 0 0; font-size: 11pt; }
        
        table.content-table {
            width: 100%;
            margin: 15px 0;
            border-collapse: collapse;
        }
        table.content-table td {
            padding: 4px 0;
            vertical-align: top;
        }

        .footer {
            margin-top: 40px;
            display: flex;
            justify-content: flex-end;
        }
        .signature-box {
            width: 250px;
            text-align: center;
        }
        
        @media print {
            .no-print { display: none; }
            body { padding: 0; }
        }
    </style>
</head>
<body>

<div class="no-print" style="margin-bottom: 20px; text-align: center;">
    <button onclick="window.print()" style="padding: 8px 20px; background: #1e3a8a; color: white; border: none; border-radius: 4px; cursor: pointer; font-weight: bold;">
        Cetak Dokumen Surat (PDF / Print)
    </button>
</div>

<div class="header">
    <h4>PEMERINTAH KABUPATEN {{ strtoupper($village->regency ?? 'BANDUNG') }}</h4>
    <h4>KECAMATAN {{ strtoupper($village->district ?? 'CIMENYAN') }}</h4>
    <h3>KANTOR DESA {{ strtoupper($village->name ?? 'SUKAMAJU SEJAHTERA') }}</h3>
    <p>{{ $village->address ?? 'Jl. Raya Desa' }} Telp: {{ $village->phone ?? '-' }} Email: {{ $village->email ?? '-' }}</p>
</div>

<div class="title-box">
    <h3>{{ strtoupper($letter->letterType->name ?? 'SURAT KETERANGAN') }}</h3>
    <p>Nomor: {{ $letter->letter_number ?? $letter->draft_number }}</p>
</div>

<p>Yang bertanda tangan di bawah ini Kepala Desa {{ $village->name ?? 'Sukamaju Sejahtera' }}, Kecamatan {{ $village->district ?? 'Cimenyan' }}, {{ $village->regency ?? 'Kabupaten Bandung' }}, menerangkan bahwa:</p>

<table class="content-table">
    <tr>
        <td style="width: 200px;">Nama Lengkap</td>
        <td style="width: 15px;">:</td>
        <td><strong>{{ strtoupper($letter->applicant_name) }}</strong></td>
    </tr>
    <tr>
        <td>NIK</td>
        <td>:</td>
        <td>{{ $letter->applicant_nik ?? '-' }}</td>
    </tr>
    <tr>
        <td>Alamat / Domisili</td>
        <td>:</td>
        <td>{{ $letter->applicant_address ?? '-' }}</td>
    </tr>
    <tr>
        <td>Keperluan</td>
        <td>:</td>
        <td>{{ $letter->purpose ?? '-' }}</td>
    </tr>
</table>

<p>Orang tersebut di atas adalah benar warga kami yang bertempat tinggal di wilayah Desa {{ $village->name ?? 'Sukamaju Sejahtera' }} dan berdasarkan catatan kami memiliki kelakuan baik serta surat ini dibuat untuk keperluan sebagaimana tercantum di atas.</p>

<p>Demikian surat keterangan ini kami buat dengan sebenarnya agar dapat dipergunakan sebagaimana mestinya.</p>

<div class="footer">
    <div class="signature-box">
        <p>{{ $village->name ?? 'Sukamaju' }}, {{ $letter->issued_at ? $letter->issued_at->format('d F Y') : date('d F Y') }}<br>Kepala Desa {{ $village->name ?? 'Sukamaju Sejahtera' }}</p>
        
        <div style="margin: 15px 0;">
            <img src="{{ $qrDataUri }}" alt="QR Verification" style="width: 90px; height: 90px;">
        </div>

        <p><strong><u>{{ strtoupper($village->head_name ?? 'H. AHMAD SYARIFUDDIN, S.IP') }}</u></strong></p>
    </div>
</div>

</body>
</html>
