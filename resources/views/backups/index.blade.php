@extends('layouts.app')

@section('title', 'Backup & Restore')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1">Backup & Restore Database Lokal</h4>
        <p class="text-muted small mb-0">Pencadangan snapshot database mandiri dan pemulihan data aman.</p>
    </div>
    <form action="{{ route('backup.create') }}" method="POST">
        @csrf
        <button type="submit" class="btn btn-primary btn-sm">
            <i class="fa-solid fa-download me-1"></i> Buat Backup Database Sekarang
        </button>
    </form>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light small">
                <tr>
                    <th>Nama File Backup</th>
                    <th>Ukuran</th>
                    <th>Tipe</th>
                    <th>Checksum SHA-256</th>
                    <th>Waktu Dibuat</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody class="small">
                @forelse($backups as $b)
                <tr>
                    <td class="fw-bold">{{ $b->filename }}</td>
                    <td>{{ round($b->size_bytes / 1024, 2) }} KB</td>
                    <td><span class="badge bg-info-subtle text-info border">{{ $b->type }}</span></td>
                    <td><code>{{ substr($b->checksum, 0, 16) }}...</code></td>
                    <td>{{ $b->created_at->format('d/m/Y H:i:s') }}</td>
                    <td class="text-end">
                        <div class="btn-group btn-group-sm">
                            <a href="{{ route('backup.download', $b->id) }}" class="btn btn-light"><i class="fa-solid fa-download text-primary"></i> Download</a>
                            <form action="{{ route('backup.restore') }}" method="POST" class="d-inline" onsubmit="return confirm('PERINGATAN: Memulihkan database akan menimpa data saat ini dengan isi backup. Lanjutkan?')">
                                @csrf
                                <input type="hidden" name="backup_id" value="{{ $b->id }}">
                                <button type="submit" class="btn btn-light text-danger"><i class="fa-solid fa-rotate-left"></i> Restore</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center text-muted py-4">Belum ada file backup tersimpan.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
