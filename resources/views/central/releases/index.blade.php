@extends('layouts.central')

@section('title', 'Pusat Rilis & Patch OTA')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1">Pusat Rilis & Distribusi Patch OTA</h4>
        <p class="text-muted small mb-0">Publikasi versi update baru untuk didownload otomatis oleh seluruh klien desktop desa.</p>
    </div>
    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#publishReleaseModal">
        <i class="fa-solid fa-cloud-arrow-up me-1"></i> Publikasikan Rilis Baru
    </button>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light small">
                <tr>
                    <th>Versi</th>
                    <th>Judul Rilis</th>
                    <th>Catatan Perubahan (Changelog)</th>
                    <th>Ukuran File</th>
                    <th>Tanggal Rilis</th>
                    <th>Status OTA</th>
                </tr>
            </thead>
            <tbody class="small">
                @forelse($releases as $rel)
                <tr>
                    <td><span class="badge bg-primary fs-6">v{{ $rel->version }}</span></td>
                    <td class="fw-bold">{{ $rel->title }}</td>
                    <td>{{ Str::limit($rel->changelog, 60) }}</td>
                    <td>{{ $rel->file_size ? number_format($rel->file_size / 1024, 1) . ' KB' : '-' }}</td>
                    <td>{{ $rel->release_date->format('d/m/Y') }}</td>
                    <td>
                        <span class="badge bg-success"><i class="fa-solid fa-broadcast-tower me-1"></i> Live OTA</span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center text-muted py-4">Belum ada versi rilis OTA dipublikasikan.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Publish Release -->
<div class="modal fade" id="publishReleaseModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('central.releases.store') }}" method="POST" enctype="multipart/form-data" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title fs-6 fw-bold"><i class="fa-solid fa-cloud-arrow-up me-2 text-primary"></i>Publikasikan Rilis Patch Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label class="form-label small fw-semibold">Versi Rilis *</label>
                        <input type="text" name="version" class="form-control form-control-sm" required placeholder="Contoh: 1.0.2">
                    </div>
                    <div class="col-6">
                        <label class="form-label small fw-semibold">Schema Version *</label>
                        <input type="number" name="schema_version" class="form-control form-control-sm" value="2" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Judul Rilis *</label>
                    <input type="text" name="title" class="form-control form-control-sm" required placeholder="Contoh: Pembaruan Modul dan Perbaikan Performa">
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Catatan Perubahan (Changelog) *</label>
                    <textarea name="changelog" class="form-control form-control-sm" rows="3" required placeholder="Tuliskan daftar fitur baru atau bugfix pada rilis ini..."></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Upload File Paket Patch (*.zip) *</label>
                    <input type="file" name="patch_file" class="form-control form-control-sm" accept=".zip,.pkg">
                    <small class="text-muted">File paket patch yang digenerate dari build_patch.ps1</small>
                </div>
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="is_mandatory" value="1" id="isMandatory">
                    <label class="form-check-label small" for="isMandatory">Wajibkan Update (Mandatory)</label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary btn-sm">Publikasikan ke Seluruh Klien</button>
            </div>
        </form>
    </div>
</div>
@endsection
