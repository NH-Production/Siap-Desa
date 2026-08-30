@extends('layouts.central')

@section('title', 'Manajemen Lisensi SAAS')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1">Manajemen Kode Lisensi & Perangkat</h4>
        <p class="text-muted small mb-0">Generator lisensi enterprise, kuota komputer per desa, dan masa aktif lisensi.</p>
    </div>
    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#generateLicenseModal">
        <i class="fa-solid fa-plus me-1"></i> Generate Lisensi Baru
    </button>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light small">
                <tr>
                    <th>Kode Lisensi</th>
                    <th>Desa Pemilik</th>
                    <th>Tier</th>
                    <th>Kuota Device</th>
                    <th>Masa Berlaku</th>
                    <th>Status</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody class="small">
                @forelse($licenses as $lic)
                <tr>
                    <td><code class="fw-bold fs-6 text-primary">{{ $lic->license_key }}</code></td>
                    <td><strong>{{ $lic->village->name ?? '-' }}</strong> ({{ $lic->village->code ?? '-' }})</td>
                    <td><span class="badge bg-indigo text-white" style="background-color:#4f46e5;">{{ $lic->tier }}</span></td>
                    <td>{{ $lic->devices->count() }} / {{ $lic->max_devices }} PC</td>
                    <td>
                        {{ $lic->issued_date->format('d/m/Y') }} s/d 
                        <strong>{{ $lic->expiry_date ? $lic->expiry_date->format('d/m/Y') : 'Lifetime' }}</strong>
                    </td>
                    <td>
                        <span class="badge bg-{{ $lic->status == 'ACTIVE' ? 'success' : 'danger' }}">{{ $lic->status }}</span>
                    </td>
                    <td class="text-end">
                        <form action="{{ route('central.licenses.toggle', $lic->uuid) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-outline-{{ $lic->status == 'ACTIVE' ? 'danger' : 'success' }}" onclick="return confirm('Ubah status lisensi ini?')">
                                {{ $lic->status == 'ACTIVE' ? 'Revoke' : 'Aktifkan' }}
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center text-muted py-4">Belum ada kode lisensi terdaftar.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Generate License -->
<div class="modal fade" id="generateLicenseModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('central.licenses.store') }}" method="POST" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title fs-6 fw-bold"><i class="fa-solid fa-key me-2 text-primary"></i>Generate Kode Lisensi SAAS</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Pilih Desa *</label>
                    <select name="central_village_id" class="form-select form-select-sm" required>
                        @foreach($villages as $v)
                            <option value="{{ $v->id }}">{{ $v->name }} (Kode: {{ $v->code }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Tier Lisensi *</label>
                    <select name="tier" class="form-select form-select-sm" required>
                        <option value="ENTERPRISE">ENTERPRISE (Fitur Penuh + Multi Device)</option>
                        <option value="STANDARD">STANDARD (Maks 3 Device)</option>
                        <option value="TRIAL">TRIAL (Uji Coba 30 Hari)</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Maksimal Jumlah Perangkat Komputer (PC) *</label>
                    <input type="number" name="max_devices" class="form-control form-control-sm" value="10" min="1" max="50" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Durasi Masa Aktif (Bulan) *</label>
                    <input type="number" name="duration_months" class="form-control form-control-sm" value="12" min="1" max="60" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Catatan</label>
                    <input type="text" name="notes" class="form-control form-control-sm" placeholder="Opsional: Keterangan kontrak desa">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary btn-sm">Generate Lisensi</button>
            </div>
        </form>
    </div>
</div>
@endsection
