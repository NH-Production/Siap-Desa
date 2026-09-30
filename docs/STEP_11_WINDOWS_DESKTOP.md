# STEP 11 — Windows Desktop Runtime & Installer

## Target

SIAP-DESA didistribusikan sebagai aplikasi Windows:

SIAP-DESA-Setup-1.0.0.exe

Aplikasi menggunakan launcher .NET 8 WPF + WebView2. Laravel tetap menjadi business application dan berjalan pada runtime lokal.

## Folder

Program Files:
C:\Program Files\SIAP-DESA\

Persistent:
C:\ProgramData\SIAP Desa\

Program Files berisi executable, source aplikasi, runtime dan updater. ProgramData berisi database, dokumen, upload, backup, log, config dan device identity.

## Installer

Installer menggunakan Inno Setup. Build script berada di installer/SIAP-DESA.iss.

## Patch

Patch berikutnya berbentuk:

SIAP-DESA-Patch-1.0.1.exe

Patch hanya mengganti application/runtime files yang ditentukan release. Database selalu diperbarui melalui Laravel migration.

## Update safety

Sebelum patch:

- cek versi;
- backup database;
- verifikasi checksum;
- stop service;
- update;
- migration;
- health check;
- start service.

Jika health check gagal, update ditandai FAILED dan backup tidak dihapus.

## Runtime

Target desktop menggunakan win-x64. Build final membutuhkan Windows build environment dengan .NET 8 SDK, WebView2 Runtime, Inno Setup, PHP runtime, dan MySQL runtime yang sudah diuji.

## Security

Desktop tidak terhubung langsung ke Central MySQL. Sinkronisasi cloud tetap melalui HTTPS API.

## Catatan

Source repository menyediakan fondasi installer dan build layout. Binary EXE final dibuat pada mesin Windows/CI Windows agar runtime Windows benar-benar teruji.
