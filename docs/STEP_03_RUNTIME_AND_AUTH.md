# STEP 03 — Desktop Runtime, Versioning, First Run & Authentication

## Target

Tahap ini membuat kontrak operasional sebelum modul bisnis dikembangkan lebih jauh.

### Runtime

- Windows desktop shell berbasis WebView2.
- Laravel/PHP berjalan di loopback.
- Local MySQL menjadi database operasional.
- Data persisten berada di ProgramData.
- Device UUID dibuat sekali dan dipertahankan sepanjang umur instalasi.

### Versioning

SIAP-DESA menggunakan:

- application version
- database schema version
- sync protocol version

Endpoint lokal:

`GET /system/version`

Contoh:

```json
{
  "product": "SIAP Desa",
  "version": "1.0.0",
  "schema_version": 1,
  "sync_protocol_version": 1,
  "device_uuid": "..."
}
```

### First Run

Alur installer:

1. Siapkan runtime.
2. Siapkan local MySQL.
3. Jalankan migration.
4. Buka `/setup`.
5. Operator mengisi identitas desa.
6. Sistem membuat device UUID.
7. Sistem membuat akun Super Admin.
8. Sistem membuat role dan permission dasar.
9. Pengguna diarahkan ke login.

### Authentication

Login menggunakan:

- username
- password
- session lokal

Role awal:

- superadmin
- admin_desa
- operator
- pegawai
- keuangan
- kepala_desa

Permission menggunakan format:

`module.action`

Contoh:

- `citizens.view`
- `citizens.create`
- `letters.approve`
- `finance.create`
- `sync.manage`

### Patch safety

Migration dijalankan menggunakan:

`php artisan migrate --force`

Patch tidak boleh menjalankan operasi destruktif tanpa migration yang eksplisit dan backup terlebih dahulu.
