# SIAP-DESA Windows Installer

Target installer: `SIAP-DESA-Setup-1.0.0.exe`

## Runtime
- PHP 8.2+
- MySQL 8.x / MariaDB 10.6+
- Laravel application
- Windows service wrapper
- Optional WebView2 desktop shell

## Directory policy

Application files:

`C:\Program Files\SIAP-DESA\`

Persistent user data:

`C:\ProgramData\SIAP-DESA\`

Persistent data includes database files, uploads, documents, backups, logs, device identity and local configuration.

## Services

- SIAP-DESA-MySQL
- SIAP-DESA-Web
- SIAP-DESA-Sync

The installer must initialize services, local database and first-run wizard. It must never require XAMPP.

## Upgrade safety

Patch packages may update application/runtime files but must not delete or replace ProgramData. Database migrations are executed transactionally where supported, followed by a health check.
