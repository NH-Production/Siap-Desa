# SIAP-DESA — Hybrid Local-First + Central Cloud

## Tujuan
SIAP-DESA berjalan penuh pada MySQL lokal ketika offline. Internet hanya diperlukan untuk sinkronisasi delta, pembaruan, backup cloud, dan layanan pusat.

## Topologi
Browser/Desktop Client -> Local PHP Application -> Local MySQL
                                      |
                                      +-> Sync Queue -> REST API -> Central MySQL

## Prinsip
1. Local database adalah sumber operasional utama untuk transaksi kantor desa.
2. Semua perubahan transaksional memiliki UUID, version, device_id, created_at, updated_at, deleted_at.
3. Perubahan masuk ke sync_queue dalam transaksi database yang sama.
4. Cloud menerima idempotent operations berdasarkan sync_event_uuid.
5. Pull menggunakan cursor/checkpoint sehingga hanya delta yang diambil.
6. Konflik tidak boleh diam-diam menimpa data; dicatat pada sync_conflicts.
7. Sinkronisasi dapat dilanjutkan setelah koneksi terputus.
8. Secret cloud tidak pernah disimpan di repository.

## Mode Operasional
### Offline
- Login lokal.
- CRUD modul.
- Cetak surat.
- Absensi QR/webcam.
- Laporan.
- Backup lokal.
- Semua perubahan masuk antrean sinkronisasi.

### Online
- Health check API.
- Push pending delta.
- Pull remote delta.
- Resolve conflict.
- Update checkpoint.
- Telemetri minimum.

## Strategi Konflik
- Insert dengan UUID sama: idempotent jika payload sama.
- Update versi lebih baru: diterapkan.
- Update versi bentrok: dicatat sebagai conflict.
- Delete membawa tombstone agar penghapusan ikut tersinkron.
- Resolusi dilakukan melalui UI administrator, bukan silent overwrite.

## Keamanan
- Password Argon2id/bcrypt.
- CSRF protection.
- Prepared statements/Eloquent.
- RBAC.
- HTTPS pada Cloud API.
- Device token terpisah dari user session.
- Secrets melalui environment.
- Audit trail untuk operasi sensitif.
