@extends('layouts.app')

@section('title', 'Detail Surat')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1">{{ $letter->letterType->name ?? 'Surat' }}</h4>
        <p class="text-muted small mb-0">Nomor: <strong>{{ $letter->letter_number ?? $letter->draft_number }}</strong></p>
    </div>
    <div class="d-flex gap-2">
        @if($letter->status !== 'APPROVED')
            <form action="{{ route('letters.approve', $letter->uuid) }}" method="POST" onsubmit="return confirm('Setujui surat ini dan alokasikan nomor surat resmi?')">
                @csrf
                <button type="submit" class="btn btn-success btn-sm">
                    <i class="fa-solid fa-check me-1"></i> Setujui & Berikan No. Resmi
                </button>
            </form>
        @endif
        <a href="{{ route('letters.print', $letter->uuid) }}" class="btn btn-primary btn-sm" target="_blank">
            <i class="fa-solid fa-print me-1"></i> Cetak Dokumen PDF
        </a>
        <a href="{{ route('letters.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="fa-solid fa-arrow-left me-1"></i> Kembali
        </a>
    </div>
</div>

<div class="row g-4">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header"><i class="fa-solid fa-file-lines me-2 text-primary"></i>Informasi Surat</div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-sm-6">
                        <small class="text-muted d-block">Nomor Resmi</small>
                        <strong class="fs-6">{{ $letter->letter_number ?? '(Masih Draft)' }}</strong>
                    </div>
                    <div class="col-sm-6">
                        <small class="text-muted d-block">Status</small>
                        <span class="badge bg-{{ $letter->status == 'APPROVED' ? 'success' : 'warning text-dark' }}">{{ $letter->status }}</span>
                    </div>
                    <div class="col-sm-6">
                        <small class="text-muted d-block">Nama Pemohon</small>
                        <strong>{{ $letter->applicant_name }}</strong>
                    </div>
                    <div class="col-sm-6">
                        <small class="text-muted d-block">NIK Pemohon</small>
                        <span>{{ $letter->applicant_nik ?? '-' }}</span>
                    </div>
                    <div class="col-12">
                        <small class="text-muted d-block">Alamat</small>
                        <span>{{ $letter->applicant_address ?? '-' }}</span>
                    </div>
                    <div class="col-12">
                        <small class="text-muted d-block">Keperluan</small>
                        <p class="mb-0 p-2 bg-light rounded border">{{ $letter->purpose ?? '-' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header"><i class="fa-solid fa-qrcode me-2 text-primary"></i>Verifikasi Keaslian</div>
            <div class="card-body text-center">
                <div class="text-muted small mb-2">Token Keaslian Dokumen:</div>
                <code>{{ $letter->qr_verification_token }}</code>
                <div class="mt-3 text-muted" style="font-size: 0.75rem;">
                    Token ini disematkan dalam QR Code surat untuk verifikasi keabsahan dokumen tanda tangan digital desa.
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
