# SIAP-DESA Updater

Patch package format:

`SIAP-DESA-Patch-1.0.1.zip`

## Transaction

1. Verify installed product and current version.
2. Verify patch SHA-256.
3. Verify minimum supported version.
4. Create database backup.
5. Stop Web and Sync services.
6. Snapshot current application files.
7. Apply patch to Program Files only.
8. Run Laravel migrations/setup tasks.
9. Start services.
10. Check `/health`.
11. Commit update on success.
12. Restore application snapshot and database backup on failure.

## Data protection

Never overwrite:

- `C:\ProgramData\SIAP-DESA\database`
- `uploads`
- `documents`
- `backups`
- `logs`
- `device.uuid`
- local configuration

