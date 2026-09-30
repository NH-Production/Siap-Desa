# STEP 05 — Perangkat Desa & Absensi QR

## Modul

- Data aparat/pegawai desa.
- NIP/NIPD dan status kepegawaian.
- Jabatan dan unit kerja.
- Riwayat jabatan.
- QR token unik per pegawai.
- Cetak kartu QR.
- Scanner webcam.
- Scanner barcode USB sebagai fallback.
- Check-in/check-out.
- Status hadir/terlambat/izin/sakit/dinas luar/alpha.
- Koreksi absensi dengan approval.
- Audit log.
- Local-first sync queue.

## Offline

Scanner tidak memerlukan internet. Browser/WebView hanya mengakses Laravel pada loopback Windows dan database MySQL lokal.

## QR

Isi QR adalah token acak, bukan NIK. Token dapat dirotasi jika kartu hilang atau dicurigai disalahgunakan.

## Sinkronisasi

Employee, EmployeePosition, Attendance, dan AttendanceCorrection menggunakan Syncable.

Perubahan lokal masuk ke sync_queue dengan status PENDING, lalu diproses Sync Engine saat koneksi cloud tersedia.

## Data integrity

Satu pegawai hanya memiliki satu record absensi per tanggal:

UNIQUE(employee_id, date)

Check-in mengisi time_in. Scan kedua mengisi time_out. Scan berikutnya ditolak jika absensi hari tersebut sudah lengkap.
