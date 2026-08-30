@extends('layouts.app')

@section('title', 'Bantuan Sosial')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1">Bantuan Sosial & BLT Desa</h4>
        <p class="text-muted small mb-0">Kelola program bansos pangan, BLT-DD, penerima manfaat, dan status penyaluran.</p>
    </div>
    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#newProgramModal">
        <i class="fa-solid fa-plus me-1"></i> Buat Program Bansos
    </button>
</div>

<div class="row g-4">
    @forelse($programs as $prog)
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span class="fw-bold">{{ $prog->name }} ({{ $prog->year }})</span>
                <span class="badge bg-success">Kuota: {{ $prog->recipients->count() }}/{{ $prog->quota }}</span>
            </div>
            <div class="card-body">
                <p class="small text-muted">{{ $prog->description ?? 'Program bantuan sosial untuk keluarga prasejahtera.' }}</p>
                <div class="mb-3 small">
                    <div>Besaran: <strong>Rp {{ number_format($prog->budget_per_recipient, 0, ',', '.') }}</strong> / KPM</div>
                    <div>Sumber Dana: <strong>{{ $prog->source }}</strong></div>
                </div>

                <h6 class="fw-bold small mb-2">Daftar Penerima Manfaat:</h6>
                <div class="table-responsive" style="max-height: 200px; overflow-y: auto;">
                    <table class="table table-sm table-bordered mb-0 small">
                        <thead>
                            <tr>
                                <th>Nama Warga</th>
                                <th>Status</th>
                                <th class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($prog->recipients as $rec)
                            <tr>
                                <td>{{ $rec->citizen->name ?? '-' }}</td>
                                <td>
                                    <span class="badge bg-{{ $rec->status == 'DISALURKAN' ? 'success' : 'warning text-dark' }}">
                                        {{ $rec->status }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    @if($rec->status !== 'DISALURKAN')
                                    <form action="{{ route('aid.recipient.distribute', $rec->uuid) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button class="btn btn-xs btn-outline-success py-0" style="font-size: 0.7rem;">Salurkan</button>
                                    </form>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="text-center text-muted">Belum ada penerima terdaftar.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer bg-white">
                <button class="btn btn-sm btn-outline-primary w-100" data-bs-toggle="modal" data-bs-target="#addRecipientModal{{ $prog->id }}">
                    <i class="fa-solid fa-user-plus me-1"></i> Tambah Penerima
                </button>
            </div>
        </div>
    </div>

    <!-- Modal Add Recipient -->
    <div class="modal fade" id="addRecipientModal{{ $prog->id }}" tabindex="-1">
        <div class="modal-dialog">
            <form action="{{ route('aid.recipient.store') }}" method="POST" class="modal-content">
                @csrf
                <input type="hidden" name="aid_program_id" value="{{ $prog->id }}">
                <div class="modal-header">
                    <h5 class="modal-title fs-6 fw-bold">Tambah Penerima: {{ $prog->name }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Pilih Warga</label>
                        <select name="citizen_id" class="form-select form-select-sm" required>
                            @foreach($citizens as $c)
                                <option value="{{ $c->id }}">{{ $c->name }} (NIK: {{ $c->nik }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm">Daftarkan</button>
                </div>
            </form>
        </div>
    </div>
    @empty
    <div class="col-12 text-center text-muted py-5">
        Belum ada program bantuan sosial.
    </div>
    @endforelse
</div>

<!-- New Program Modal -->
<div class="modal fade" id="newProgramModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('aid.program.store') }}" method="POST" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title fs-6 fw-bold">Buat Program Bansos Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Nama Program *</label>
                    <input type="text" name="name" class="form-control form-control-sm" required placeholder="Contoh: BLT Dana Desa 2026">
                </div>
                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label class="form-label small fw-semibold">Tahun</label>
                        <input type="number" name="year" class="form-control form-control-sm" value="{{ date('Y') }}" required>
                    </div>
                    <div class="col-6">
                        <label class="form-label small fw-semibold">Kuota Penerima</label>
                        <input type="number" name="quota" class="form-control form-control-sm" value="50" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Besaran Bantuan / Warga (Rp)</label>
                    <input type="number" name="budget_per_recipient" class="form-control form-control-sm" value="300000" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Sumber Dana</label>
                    <input type="text" name="source" class="form-control form-control-sm" value="DANA_DESA">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary btn-sm">Buat Program</button>
            </div>
        </form>
    </div>
</div>
@endsection
