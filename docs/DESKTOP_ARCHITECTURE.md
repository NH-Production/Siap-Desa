# SIAP-DESA Desktop Architecture

## 1. Purpose

SIAP-DESA is distributed to village offices as a Windows desktop application. The local installation is self-contained and must continue operating without internet access.

The desktop package is intentionally separated into:

- application binaries
- runtime components
- persistent village data
- synchronization state
- updater state

User data must never live inside `Program Files` and must never be replaced by an application patch.

## 2. Target topology

```
┌──────────────────────── SIAP-DESA Windows ────────────────────────┐
│                                                                    │
│  SIAP-DESA.exe (WebView2 shell)                                    │
│          │                                                         │
│          ├── Local Laravel/PHP application                         │
│          │                                                         │
│          └── Sync/Updater controller                               │
│                         │                                          │
│                    Local MySQL                                     │
│                         │                                          │
│                    sync_queue                                      │
└─────────────────────────┬──────────────────────────────────────────┘
                          │ HTTPS only when available
                          ▼
                  Central SIAP-DESA API
                          │
                          ▼
                    Central MySQL
```

The desktop application must never connect directly from the client to the central MySQL server.

## 3. Windows filesystem contract

### Application

`C:\Program Files\SIAP-DESA\`

Contains only versioned application/runtime files:

- `SIAP-DESA.exe`
- `runtime\php\`
- `runtime\mysql\`
- `app\`
- `public\`
- `modules\`
- `sync\`
- `updater\`

### Persistent data

`C:\ProgramData\SIAP-DESA\`

Contains:

- `database\`
- `uploads\`
- `documents\`
- `backups\`
- `logs\`
- `config\`
- `device.uuid`

A patch may replace files under Program Files, but it must not delete or replace ProgramData.

## 4. Runtime processes

The production installer targets these logical services:

1. **SIAP-DESA-MySQL** — local database service.
2. **SIAP-DESA-Web** — local Laravel/PHP web runtime.
3. **SIAP-DESA-Sync** — background synchronization worker.

The desktop shell opens the local application through WebView2.

## 5. Offline-first rules

All core business operations are local transactions. Internet availability is never a prerequisite for:

- login
- population management
- family management
- employee management
- attendance
- correspondence
- public services
- finance
- assets
- social assistance
- reporting
- backup

Synchronization is an asynchronous process.

## 6. Version contracts

Every release carries four independent versions:

- Application version: user-facing release, e.g. `1.2.4`
- Database schema version: local database structure, e.g. `1.1.0`
- Sync protocol version: payload/endpoint compatibility, e.g. `1.0`
- Installer version: package/build revision

An application patch must verify compatibility before applying migrations.

## 7. Security

- Local web server binds to loopback only.
- Central communication uses HTTPS.
- Central MySQL credentials never ship to the desktop.
- API credentials are stored outside the application directory.
- Local secrets are stored under ProgramData with restrictive ACLs.
- Every synchronization event has a UUID and is processed idempotently.
- Audit logs are retained locally and synchronized when appropriate.

## 8. Upgrade invariant

The following invariant must always hold:

> Application updates may change executable/code/runtime files, but may not erase village data, documents, uploads, backups, device identity, or synchronization history.

Before a patch, the updater creates a database backup and records the current application/schema versions.
