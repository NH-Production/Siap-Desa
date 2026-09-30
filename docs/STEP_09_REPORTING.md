# STEP 09 — Reporting Center & Dokumen Resmi

## Registry laporan

Pusat laporan menyediakan:

- Kependudukan.
- Keuangan/APBDes.
- Absensi aparat.
- Aset/inventaris.
- Bansos/BLT-DD.
- Persuratan.

## Filter

- Bulan untuk absensi.
- Tahun untuk bansos.
- Rentang tanggal untuk persuratan.
- Filter tambahan dapat ditambahkan tanpa mengubah kontrak report URL.

## Output

Setiap laporan mempunyai halaman A4 yang bisa langsung dicetak. Endpoint PDF menggunakan Dompdf yang sudah menjadi dependency aplikasi sehingga dapat berjalan offline pada desktop.

Pola PDF:

GET /reports/pdf/{type}

Contoh:

/reports/pdf/citizens
/reports/pdf/finance
/reports/pdf/attendance
/reports/pdf/assets
/reports/pdf/aid
/reports/pdf/letters

## Audit

Setiap pembuatan laporan dicatat ke audit log dengan tipe REPORT dan parameter filter yang digunakan.

## Prinsip privasi

Laporan internal dapat memuat data lengkap sesuai hak akses. Endpoint publik tidak menggunakan report center; verifikasi surat publik tetap memakai endpoint verifikasi khusus dengan data minimum.

## Desktop

PDF dibuat dari database lokal sehingga tidak memerlukan internet. Browser/WebView Windows dapat menampilkan PDF untuk dicetak atau disimpan.
