# STEP 13 — Central Cloud API + MySQL

Central tidak menerima koneksi MySQL langsung dari desktop.

Endpoint:
- POST /api/v1/sync/push
- GET /api/v1/sync/pull

Push:
- validasi protocol;
- validasi village/device UUID;
- device harus ACTIVE;
- event UUID idempotent;
- version diperiksa;
- event tersimpan pada central sync journal;
- device last_seen/last_sync diperbarui.

Pull:
- validasi device;
- menerima cursor;
- mengambil delta per village;
- maksimum 100 event per halaman;
- mengembalikan next_cursor.

Central API menggunakan MySQL dan tidak lagi meneruskan sinkronisasi ke Supabase.

Catatan implementasi berikutnya: production central deployment sebaiknya memakai database khusus cloud dan endpoint sync dipisahkan dari database operasional desktop. API key/device token harus di-hash dan dapat dicabut.
