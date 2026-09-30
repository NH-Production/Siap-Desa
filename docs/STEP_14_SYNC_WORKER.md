# STEP 14 — Automatic Sync Worker

Worker command:

php artisan siap:sync

One-shot diagnostic:

php artisan siap:sync --once

Production Windows service runs the command continuously. Each cycle:

1. check Central API;
2. if offline, keep local application fully operational;
3. if online, push pending/failed events;
4. pull remote delta;
5. update checkpoint;
6. wait for configured interval.

The worker never connects directly to Central MySQL.

## Windows Service

The production installer will register the worker as SIAP-DESA-Sync. The service should run under a dedicated Windows service account with access only to SIAP-DESA ProgramData and application runtime.

## Status

The UI can derive:
- OFFLINE: API health failed;
- ONLINE: API reachable and no active sync;
- SYNCING: worker is processing;
- ERROR: repeated failures;
- CONFLICT: unresolved sync conflicts exist.
