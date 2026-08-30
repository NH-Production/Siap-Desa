@extends('layouts.app')

@section('title', 'Absensi Pegawai')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1">Rekapitulasi Absensi Pegawai</h4>
        <p class="text-muted small mb-0">Catatan kehadiran aparat desa via QR Code & verifikasi manual.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('attendance.scanner') }}" class="btn btn-success btn-sm">
            <i class="fa-solid fa-qrcode me-1"></i> Buka Scanner Kamera
        </a>
        <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#manualModal">
            <i class="fa-solid fa-pen-to-square me-1"></i> Absensi Manual
        </button>
    </div>
</div>

<!-- Date Filter Card -->
<div class="card mb-4 p-3 bg-light border">
    <form action="{{ route('attendance.index') }}" method="GET" class="row g-2 align-items-center">
        <div class="col-md-4">
            <label class="form-label small fw-semibold mb-0">Pilih Tanggal:</label>
            <input type="date" name="date" class="form-control form-control-sm" value="{{ $date }}" onchange="this.form.submit()">
        </div>
    </form>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light small">
                <tr>
                    <th>Nama Pegawai</th>
                    <th>Jabatan</th>
                    <th>Jam Masuk</th>
                    <th>Jam Pulang</th>
                    <th>Status</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody class="small">
                @forelse($attendances as $att)
                <tr>
                    <td class="fw-bold">{{ $att->employee->name ?? '-' }}</td>
                    <td>{{ $att->employee->position ?? '-' }}</td>
                    <td>{{ $att->time_in ?? '-' }}</td>
                    <td>{{ $att->time_out ?? '-' }}</td>
                    <td>
                        <span class="badge bg-{{ $att->status == 'HADIR' ? 'success' : ($att->status == 'TERLAMBAT' ? 'warning' : 'info') }}">
                            {{ $att->status }}
                        </span>
                    </td>
                    <td>{{ $att->notes ?? '-' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center text-muted py-4">Belum ada data absensi untuk tanggal ini.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Manual Modal -->
<div class="modal fade" id="manualModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('attendance.manual') }}" method="POST" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title fs-6 fw-bold">Input Absensi Manual</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Pegawai *</label>
                    <select name="employee_id" class="form-select form-select-sm" required>
                        @foreach($employees as $emp)
                            <option value="{{ $emp->id }}">{{ $emp->name }} ({{ $emp->position }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Tanggal *</label>
                    <input type="date" name="date" class="form-control form-control-sm" value="{{ $date }}" required>
                </div>
                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label class="form-label small fw-semibold">Jam Masuk</label>
                        <input type="time" name="time_in" class="form-control form-control-sm" value="08:00">
                    </div>
                    <div class="col-6">
                        <label class="form-label small fw-semibold">Jam Pulang</label>
                        <input type="time" name="time_out" class="form-control form-control-sm">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Status *</label>
                    <select name="status" class="form-select form-select-sm" required>
                        <option value="HADIR">HADIR</option>
                        <option value="TERLAMBAT">TERLAMBAT</option>
                        <option value="IZIN">IZIN</option>
                        <option value="SAKIT">SAKIT</option>
                        <option value="DINAS_LUAR">DINAS LUAR</option>
                        <option value="ALPHA">ALPHA</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Keterangan</label>
                    <input type="text" name="notes" class="form-control form-control-sm" placeholder="Catatan opsional">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary btn-sm">Simpan Absensi</button>
            </div>
        </form>
    </div>
</div>
@endsection
