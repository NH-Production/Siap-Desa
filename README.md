# SIAP DESA (Sistem Informasi Administrasi Pemerintahan Desa)

[![SIAP Desa CI/CD](https://github.com/NH-Production/Siap-Desa/actions/workflows/build-release.yml/badge.svg)](https://github.com/NH-Production/Siap-Desa/actions)
[![License: Proprietary](https://img.shields.io/badge/License-SAAS_Enterprise-blue.svg)](https://github.com/NH-Production/Siap-Desa)
[![PHP Version](https://img.shields.io/badge/PHP-8.3_x64_Portable-777bb4.svg)](https://php.net)
[![Platform](https://img.shields.io/badge/Platform-Windows_10_/_11-0078d7.svg)](https://microsoft.com/windows)

**SIAP Desa** adalah platform manajemen administrasi pemerintahan desa cerdas dengan arsitektur **Hybrid Local-First + Central Cloud SAAS**. Aplikasi dapat berjalan 100% secara offline di komputer kantor desa dan secara otomatis melakukan sinkronisasi data (*Delta Synchronization*) ke **Central Cloud (Supabase PostgreSQL)** ketika terhubung ke internet.

---

## 🏛️ Modul-Modul Sistem

1. **Dashboard & Analitik**: Monitoring statistik kependudukan, posisi kas APBDes, dan kehadiran aparat desa.
2. **Kependudukan & Kartu Keluarga (KK)**: Manajemen data NIK, hubungan keluarga, filter cerdas, import/export CSV.
3. **Kepegawaian & Absensi Kamera QR Code**: Struktur organisasi aparat desa, generator Kartu Absensi Cetak QR, dan scanner kamera webcam dengan audio feedback bip otomatis.
4. **Persuratan & Pelayanan Publik**: Master surat desa resmi (SKTM, SKU, SKDOM, SKCK, Kelahiran, Kematian) dengan nomor otomatis dan QR code verification.
5. **Keuangan APBDes & SPJ**: Bagan Akun Standar (CoA), pagu anggaran tahunan, buku kas umum, dan laporan SPJ.
6. **Aset & Inventaris Desa**: Pencatatan barang milik desa, kode aset, kondisi fisik, dan histori mutasi.
7. **Bantuan Sosial (Bansos & BLT-DD)**: Penyaluran bantuan sosial dan kuota penerima manfaat.
8. **Pusat Laporan Multi-Modul**: Format cetak resmi siap print & PDF.
9. **Central Server SAAS Panel**: Pengendali lisensi multi-desa, isolasi tenant, live telemetri, dan distribusi rilis patch OTA (Over-The-Air).
10. **Backup, Restore, & Diagnostik**: Snapshot backup database lokal 1-klik terkompresi dan audit trail log.

---

## 📦 Paket Rilis Executable

Proyek ini menyediakan 2 paket installer mandiri terpisah:
1. **`SIAP_Desa_Client_Setup_v1.0.0.exe`**: Installer untuk komputer kantor desa (Port `8088`).
2. **`SIAP_Desa_Central_Server_Setup_v1.0.0.exe`**: Installer untuk server cloud / admin pusat SAAS (Port `8090`).
3. **`SIAP_Desa_Patch_v1.0.1.exe`**: Paket patch pembaruan instan tanpa install ulang.

---

## 🚀 Repository & CI/CD
- **Repository URL**: `https://github.com/NH-Production/Siap-Desa`
- **Central Supabase Layer**: Terintegrasi menggunakan Supabase Publishable Key untuk otentikasi delta sync.
- **Automated OTA Release**: GitHub Actions workflow secara otomatis mengompilasi dan mempublikasikan installer EXE ke GitHub Releases pada setiap tag versi (`v*`).

---
Copyright (c) 2026 NH Production. All rights reserved.
