# SIAP-DESA Release and Patch Strategy

## Release channels

SIAP-DESA uses semantic application versions:

- `1.0.0` — initial installer
- `1.0.1` — bug-fix patch
- `1.1.0` — feature release
- `2.0.0` — major release

## Package types

### Full installer

Filename:

`SIAP-DESA-Setup-1.0.0.exe`

A full installer provisions:

- desktop shell
- PHP runtime
- local MySQL runtime
- application files
- Windows services
- initial database
- local configuration
- device identity

### Patch installer

Filename:

`SIAP-DESA-Patch-1.0.1.exe`

A patch contains only changed application/runtime assets and migration scripts.

It must:

1. verify installed SIAP-DESA version
2. verify Windows prerequisites
3. create a backup
4. stop SIAP-DESA services
5. verify patch integrity
6. replace application files
7. execute database migrations
8. update version metadata
9. start services
10. run health checks
11. rollback application files when a critical step fails

## Data safety

The patch process must never:

- drop operational tables
- overwrite `ProgramData`
- reset village configuration
- regenerate the device UUID
- delete sync queue entries
- replace uploaded documents

Destructive database migrations require an explicit migration strategy and backup validation.

## Patch manifest

The updater reads a signed manifest similar to:

```json
{
  "product": "SIAP-DESA",
  "version": "1.0.1",
  "minimum_version": "1.0.0",
  "schema_version": "1.0.1",
  "sync_protocol": "1.0",
  "package": "SIAP-DESA-Patch-1.0.1.exe",
  "sha256": "<sha256>",
  "mandatory": false
}
```

The production updater must verify the SHA-256 checksum and, when signing infrastructure is enabled, the package signature.

## Rollback

The updater keeps:

- previous application version
- database backup
- migration execution log
- updater log

A failed application migration blocks normal startup and places the installation into recovery mode.

## Compatibility

A patch is valid only when:

`installed_version >= minimum_version`

and the migration path is known.

The updater must reject a patch when the current schema or sync protocol is incompatible.
