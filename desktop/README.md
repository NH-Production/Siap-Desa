# SIAP-DESA Windows Desktop

Launcher native Windows berbasis .NET 8 + WebView2.

Runtime production akan dibundel di folder aplikasi:

- runtime/php/php.exe
- runtime/mysql/
- runtime/service/
- public/
- updater/

Data pengguna tetap di C:\\ProgramData\\SIAP Desa dan tidak ikut ditimpa patch.

Launcher memulai web runtime lokal pada 127.0.0.1:8088 dan menampilkannya melalui WebView2.

> Build EXE final dilakukan pada Windows build runner/PC Windows dengan .NET 8 SDK dan WebView2 Runtime tersedia.