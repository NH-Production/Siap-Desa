# STEP 12 — Delta Synchronization

## Prinsip

Desktop hanya berkomunikasi dengan Central melalui HTTPS API. Desktop tidak boleh membuka koneksi langsung ke Central MySQL.

## Push

Perubahan lokal direkam ke sync_queue dengan:

- sync_event_uuid
- village_uuid
- device_uuid
- table_name
- record_uuid
- operation
- record_version
- payload

Batch dikirim berdasarkan urutan event. Server wajib idempotent terhadap sync_event_uuid.

## Pull

Device menyimpan remote_cursor pada sync_checkpoints. Pull hanya meminta perubahan setelah cursor terakhir. Setelah seluruh perubahan berhasil diterapkan, cursor diperbarui.

## Retry

Kegagalan koneksi tidak menghapus queue. Event tetap tersedia untuk percobaan berikutnya sampai batas attempts.

## Conflict

Jika server mendeteksi versi yang tidak kompatibel, event diberi status CONFLICT dan dibuatkan record sync_conflicts. Conflict harus diselesaikan secara eksplisit; tidak boleh diam-diam menimpa data.

## Remote apply

Perubahan dari server diterapkan tanpa membuat event sync baru. Ini mencegah loop:

Cloud → Local → Cloud → Local.

## Tombstone

DELETE harus direpresentasikan sebagai event dengan record_uuid dan version sehingga penghapusan tetap dapat direplikasi.

## Urutan

PUSH → PULL → checkpoint update.

Jika PUSH gagal, PULL tetap dapat dijalankan sesuai kebijakan client. Cursor hanya maju setelah data pull berhasil diproses.

## Automatic Sync

Windows Sync Service akan menjalankan full cycle secara periodik:

1. cek koneksi API;
2. push pending queue;
3. pull delta;
4. update device last_seen/last_sync;
5. catat hasil;
6. tunggu interval berikutnya.

