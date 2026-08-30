@extends('layouts.app')

@section('title', 'Pelayanan Publik')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1">Pusat Pelayanan Publik Desa</h4>
        <p class="text-muted small mb-0">Pendaftaran permohonan layanan administrasi desa dan tracking status berkas.</p>
    </div>
    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#newServiceModal">
        <i class="fa-solid fa-plus me-1"></i> Permohonan Layanan Baru
    </button>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light small">
                <tr>
                    <th>Layanan</th>
                    <th>Pemohon</th>
                    <th>Kontak</th>
                    <th>Tgl Diajukan</th>
                    <th>Status</th>
                    <th class="text-end">Update Status</th>
                </tr>
            </thead>
            <tbody class="small">
                @forelse($requests as $req)
                <tr>
                    <td class="fw-bold">{{ $req->service->name ?? '-' }}</td>
                    <td>{{ $req->applicant_name }} (NIK: {{ $req->applicant_nik ?? '-' }})</td>
                    <td>{{ $req->applicant_phone ?? '-' }}</td>
                    <td>{{ $req->created_at->format('d/m/Y H:i') }}</td>
                    <td>
                        <span class="badge bg-{{ $req->status == 'COMPLETED' ? 'success' : ($req->status == 'APPROVED' ? 'primary' : 'warning text-dark') }}">
                            {{ $req->status }}
                        </span>
                    </td>
                    <td class="text-end">
                        <form action="{{ route('services.status', $req->uuid) }}" method="POST" class="d-inline-flex gap-1">
                            @csrf
                            @method('PUT')
                            <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                                <option value="SUBMITTED" {{ $req->status == 'SUBMITTED' ? 'selected' : '' }}>SUBMITTED</option>
                                <option value="VERIFIED" {{ $req->status == 'VERIFIED' ? 'selected' : '' }}>VERIFIED</option>
                                <option value="APPROVED" {{ $req->status == 'APPROVED' ? 'selected' : '' }}>APPROVED</option>
                                <option value="COMPLETED" {{ $req->status == 'COMPLETED' ? 'selected' : '' }}>COMPLETED</option>
                                <option value="REJECTED" {{ $req->status == 'REJECTED' ? 'selected' : '' }}>REJECTED</option>
                            </select>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center text-muted py-4">Belum ada permohonan layanan.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- New Service Modal -->
<div class="modal fade" id="newServiceModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('services.store') }}" method="POST" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title fs-6 fw-bold">Daftar Permohonan Layanan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Pilih Jenis Layanan *</label>
                    <select name="service_id" class="form-select form-select-sm" required>
                        @foreach($services as $s)
                            <option value="{{ $s->id }}">{{ $s->name }} (Estimasi: {{ $s->processing_days }} Hari)</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Nama Pemohon *</label>
                    <input type="text" name="applicant_name" class="form-control form-control-sm" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">NIK Pemohon</label>
                    <input type="text" name="applicant_nik" class="form-control form-control-sm" maxlength="16">
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Nomor WhatsApp / HP</label>
                    <input type="text" name="applicant_phone" class="form-control form-control-sm">
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Catatan</label>
                    <textarea name="notes" class="form-control form-control-sm" rows="2"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary btn-sm">Daftarkan Layanan</button>
            </div>
        </form>
    </div>
</div>
@endsection
