# STEP 10 — Backup, Restore, Diagnostics & Reliability

## Backup MySQL

Backup lokal menggunakan mysqldump dan menghasilkan file SQL di:

C:\\ProgramData\\SIAP Desa\\backups\\

Setiap backup menyimpan checksum SHA-256 dan metadata pada tabel backups.

## Restore

Restore menggunakan mysql client ke database lokal. Operasi restore membutuhkan permission khusus system.backup.restore.

Restore tidak dilakukan dengan menyalin file database secara langsung.

## Diagnostics

Diagnostic Center memeriksa:
- koneksi database lokal;
- versi aplikasi/schema/sync protocol;
- perangkat;
- desa;
- pending/failed sync queue;
- unresolved conflicts;
- kapasitas disk;
- PHP/runtime;
- lokasi data.

## Recovery

1. Hentikan service aplikasi/sync.
2. Buat backup kondisi saat ini jika database masih dapat dibaca.
3. Restore SQL backup.
4. Jalankan migration yang diperlukan.
5. Jalankan health check.
6. Periksa sync queue dan conflicts.
7. Jalankan sync ulang.

## Reliability

Database operasional berada di MySQL lokal. Backup tidak menggunakan SQLite. Data pengguna berada di ProgramData dan tidak boleh ditimpa oleh patch aplikasi.

## Desktop EXE

Backup → Stop Services → Patch Files → Migration → Health Check → Start Services.

Jika health check gagal, updater harus menahan status update sebagai FAILED dan mempertahankan backup rollback.
