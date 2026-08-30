@extends('layouts.app')

@section('title', 'Buat Surat Baru')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1">Penerbitan Surat Pelayanan</h4>
        <p class="text-muted small mb-0">Pilih jenis surat dan masukkan informasi permohonan warga.</p>
    </div>
    <a href="{{ route('letters.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="fa-solid fa-arrow-left me-1"></i> Kembali
    </a>
</div>

<div class="card">
    <form action="{{ route('letters.store') }}" method="POST" class="card-body p-4">
        @csrf
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label small fw-semibold">Pilih Jenis Surat *</label>
                <select name="letter_type_id" class="form-select" required>
                    @foreach($letterTypes as $lt)
                        <option value="{{ $lt->id }}">{{ $lt->name }} ({{ $lt->code }})</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label small fw-semibold">Pilih dari Data Penduduk (Opsional)</label>
                <select name="citizen_id" id="citizenSelect" class="form-select">
                    <option value="">-- Input Manual / Pilih Penduduk --</option>
                    @foreach($citizens as $c)
                        <option value="{{ $c->id }}" data-name="{{ $c->name }}" data-nik="{{ $c->nik }}" data-address="{{ $c->address }}">
                            {{ $c->name }} (NIK: {{ $c->nik }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-6">
                <label class="form-label small fw-semibold">Nama Pemohon *</label>
                <input type="text" name="applicant_name" id="applicantName" class="form-control" required placeholder="Nama Lengkap Pemohon">
            </div>
            <div class="col-md-6">
                <label class="form-label small fw-semibold">NIK Pemohon</label>
                <input type="text" name="applicant_nik" id="applicantNik" class="form-control" maxlength="16" placeholder="16 digit NIK">
            </div>

            <div class="col-md-12">
                <label class="form-label small fw-semibold">Alamat Pemohon</label>
                <input type="text" name="applicant_address" id="applicantAddress" class="form-control" placeholder="Alamat lengkap domisili pemohon">
            </div>

            <div class="col-md-12">
                <label class="form-label small fw-semibold">Keperluan / Keterangan Surat *</label>
                <textarea name="purpose" class="form-control" rows="3" required placeholder="Jelaskan keperluan pembuatan surat (Contoh: Pengajuan Beasiswa, Persyaratan Bank, dll)"></textarea>
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
            <a href="{{ route('letters.index') }}" class="btn btn-light px-4">Batal</a>
            <button type="submit" class="btn btn-primary px-4">
                <i class="fa-solid fa-save me-1"></i> Simpan Draft Surat
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('citizenSelect').addEventListener('change', function() {
    const opt = this.options[this.selectedIndex];
    if (opt && opt.value) {
        document.getElementById('applicantName').value = opt.dataset.name || '';
        document.getElementById('applicantNik').value = opt.dataset.nik || '';
        document.getElementById('applicantAddress').value = opt.dataset.address || '';
    }
});
</script>
@endpush
