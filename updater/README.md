# SIAP-DESA Updater

The updater is a separate executable/package from the main application.

Target artifact:

`SIAP-DESA-Patch-1.0.1.exe`

## Required update sequence

```
check version
    ↓
backup local database
    ↓
stop services
    ↓
verify package checksum/signature
    ↓
replace Program Files application files
    ↓
run migrations
    ↓
update version metadata
    ↓
start services
    ↓
health check
    ↓
success / recovery
```

## Never update

Do not replace these paths during a normal patch:

- `C:\ProgramData\SIAP-DESA\database`
- `C:\ProgramData\SIAP-DESA\uploads`
- `C:\ProgramData\SIAP-DESA\documents`
- `C:\ProgramData\SIAP-DESA\backups`
- `C:\ProgramData\SIAP-DESA\device.uuid`

## Recovery

If the migration or health check fails:

1. stop services
2. restore application files from the previous package
3. restore database backup only when required by migration failure
4. write recovery details to the updater log
5. keep the installation in recovery state until a compatible patch is applied
