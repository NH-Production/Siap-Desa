# STEP 15 — Central Management & Device Registry

## Tujuan

Central menjadi tempat administrator pusat memonitor desa dan komputer SIAP-DESA tanpa mengakses database lokal secara langsung.

## Device

Device mempunyai:

- UUID unik;
- village_uuid;
- device_name;
- app_version;
- schema_version;
- last_seen_at;
- last_sync_at;
- status active/blocked/retired.

## Status

Status operasional dihitung:

- ONLINE jika device aktif dan last_seen kurang dari 3 menit;
- OFFLINE jika tidak ada heartbeat dalam 3 menit;
- BLOCKED jika administrator memblokir device;
- RETIRED jika perangkat sudah dipensiunkan.

## Central Device Controller

Controller menyediakan operasi:

- daftar device;
- filter desa/status;
- registrasi device;
- block/activate;
- retire.

View berada pada resources/views/central/devices/index.blade.php.

## Route

Route yang diperlukan:

GET /central/devices
POST /central/devices/register
POST /central/devices/{device}/toggle
POST /central/devices/{device}/retire

Penambahan route harus ditempatkan di dalam central route group dan dilindungi middleware autentikasi/otorisasi administrator pusat.

## Keamanan

Device yang BLOCKED/RETIRED tidak boleh melakukan sync. Central API memvalidasi village_uuid + device_uuid pada setiap push/pull.

## Release Management

Device registry menjadi dasar berikutnya untuk:

- melihat versi aplikasi;
- mengetahui device yang perlu patch;
- minimum supported version;
- mandatory update;
- release channel;
- patch rollout per desa.

