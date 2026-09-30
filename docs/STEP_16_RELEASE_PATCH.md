# STEP 16 — Release & Patch Management

## Release artifact

Format:

SIAP-DESA-Patch-1.0.1.zip

The final Windows distribution wrapper remains:

SIAP-DESA-Patch-1.0.1.exe

The Central release registry stores:

- version
- minimum_version
- database schema
- sync protocol
- changelog
- package filename
- package size
- SHA-256
- mandatory flag
- publication date.

## Safety

A patch must never replace C:\\ProgramData\\SIAP Desa.

Before applying a patch the updater creates a database backup, verifies SHA-256, checks minimum_version, stops application services, installs application files, runs migrations, runs health checks, and restarts services.

## Compatibility

The updater must reject a package when:

- product is not SIAP-DESA;
- current version is below minimum_version;
- package checksum fails;
- sync protocol is unsupported;
- migration precondition fails.

## Rollout

Central can use device version information to identify installations that need an update. Mandatory releases must be clearly presented to the local operator and can be enforced by the updater after a successful backup.

## Database

Central release metadata is stored in database/mysql/002_central_release.sql.
