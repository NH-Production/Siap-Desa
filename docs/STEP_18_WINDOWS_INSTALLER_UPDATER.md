# STEP 18 — Windows Installer & Patch Updater

## Release targets

- Full installer: `SIAP-DESA-Setup-1.0.0.exe`
- Patch: `SIAP-DESA-Patch-1.0.1.zip` (compiled patch EXE may wrap this transaction)
- Installation root: `C:\\Program Files\\SIAP-DESA\\`
- Persistent data: `C:\\ProgramData\\SIAP-DESA\\`

## Runtime services

1. SIAP-DESA-MySQL — local database runtime.
2. SIAP-DESA-Web — Laravel/PHP local web runtime.
3. SIAP-DESA-Sync — background delta synchronization worker.

## First installation

1. Install bundled runtime.
2. Create local MySQL database.
3. Import foundation and business schema.
4. Publish Laravel application/runtime.
5. Create ProgramData directories.
6. Generate device UUID.
7. Start Web and Sync services.
8. Open the first-run wizard.
9. Administrator creates village and first administrator account.
10. Run health check.

## Patch transaction

A patch is application-only. It must not overwrite operational data.

1. Check installed version.
2. Verify SHA-256.
3. Check `minimum_version`.
4. Create database backup.
5. Stop Web and Sync.
6. Snapshot Program Files application directory.
7. Apply patch.
8. Apply schema changes.
9. Start services.
10. Verify `/health`.
11. Keep update on success.
12. Restore application snapshot and database backup on failure.

## Data protection

Never replace or delete:

`C:\\ProgramData\\SIAP-DESA\\database`
`C:\\ProgramData\\SIAP-DESA\\uploads`
`C:\\ProgramData\\SIAP-DESA\\documents`
`C:\\ProgramData\\SIAP-DESA\\backups`
`C:\\ProgramData\\SIAP-DESA\\logs`
`C:\\ProgramData\\SIAP-DESA\\device.uuid`

## Current status

The repository now contains the release staging scripts and updater contract. The actual binary EXE still has to be compiled on a Windows build machine with the selected installer compiler and bundled PHP/MySQL runtime.
