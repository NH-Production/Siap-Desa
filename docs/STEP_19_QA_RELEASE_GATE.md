# STEP 19 — QA & Release Gate

A release is not considered ready only because the source has been committed. The following checks must pass on a Windows build/test machine.

## A. Installation

- [ ] Windows installer completes without XAMPP.
- [ ] Local MySQL service starts.
- [ ] Local web service starts.
- [ ] Sync service starts.
- [ ] ProgramData directory is created.
- [ ] Device UUID persists after restart.
- [ ] First-run wizard opens.

## B. Authentication

- [ ] Administrator account can be created.
- [ ] Login works with password_hash storage.
- [ ] Logout invalidates session.
- [ ] Blocked user cannot access the application.
- [ ] Role permissions are enforced.

## C. Core modules

- [ ] Dashboard.
- [ ] Profil desa.
- [ ] Penduduk CRUD.
- [ ] CSV import/export.
- [ ] Kartu keluarga and members.
- [ ] Aparatur/pegawai.
- [ ] QR attendance check-in/check-out.
- [ ] Attendance correction.
- [ ] Surat and numbering.
- [ ] QR letter verification.
- [ ] Public service requests.
- [ ] APBDes accounts/budget.
- [ ] Cash transactions.
- [ ] SPJ.
- [ ] Assets and mutation history.
- [ ] Social assistance and distribution.
- [ ] Reports/PDF.
- [ ] Backup/restore.

## D. Offline-first

1. Disconnect internet.
2. Add/edit records.
3. Verify the application remains usable.
4. Confirm sync queue records are PENDING.
5. Reconnect internet.
6. Confirm queue moves to SYNCED.
7. Confirm remote cursor/checkpoint advances.

## E. Conflict handling

- [ ] Modify the same UUID on local and central.
- [ ] Confirm conflict is recorded.
- [ ] Confirm it does not silently overwrite data.
- [ ] Resolve LOCAL/REMOTE/MERGED.
- [ ] Confirm audit log.

## F. Patch safety

- [ ] Install 1.0.0.
- [ ] Create real local records.
- [ ] Apply 1.0.1 patch.
- [ ] Confirm records and uploads remain intact.
- [ ] Test failed patch rollback.
- [ ] Verify SHA-256 before applying.

## G. Release decision

Only after all required checks pass should the Windows build machine produce:

- SIAP-DESA-Setup-1.0.0.exe
- SIAP-DESA-Patch-1.0.1.exe

The compiled binaries are release artifacts and should not be committed to the source repository.
