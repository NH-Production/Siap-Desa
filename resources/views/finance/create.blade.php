@extends('layouts.app')

@section('title', 'Catat Transaksi Keuangan')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1">Catat Transaksi Kas</h4>
        <p class="text-muted small mb-0">Input penerimaan atau pengeluaran dana APBDes.</p>
    </div>
    <a href="{{ route('finance.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="fa-solid fa-arrow-left me-1"></i> Kembali
    </a>
</div>

<div class="card">
    <form action="{{ route('finance.store') }}" method="POST" class="card-body p-4">
        @csrf
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label small fw-semibold">Tanggal Transaksi *</label>
                <input type="date" name="transaction_date" class="form-control" required value="{{ date('Y-m-d') }}">
            </div>
            <div class="col-md-4">
                <label class="form-label small fw-semibold">Jenis Arus Kas *</label>
                <select name="type" class="form-select" required>
                    <option value="PENERIMAAN">Penerimaan (+ Masuk)</option>
                    <option value="PENGELUARAN">Pengeluaran (- Keluar)</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label small fw-semibold">Kode Rekening Akun *</label>
                <select name="account_id" class="form-select" required>
                    @foreach($accounts as $acc)
                        <option value="{{ $acc->id }}">{{ $acc->code }} - {{ $acc->name }} ({{ $acc->type }})</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-6">
                <label class="form-label small fw-semibold">Nominal Transaksi (Rp) *</label>
                <input type="number" step="0.01" name="amount" class="form-control" required placeholder="Contoh: 15000000">
            </div>
            <div class="col-md-6">
                <label class="form-label small fw-semibold">Metode Pembayaran *</label>
                <select name="payment_method" class="form-select" required>
                    <option value="TUNAI">Kas Tunai Bendahara</option>
                    <option value="TRANSFER">Transfer Rekening Kas Desa (Bank)</option>
                </select>
            </div>

            <div class="col-md-6">
                <label class="form-label small fw-semibold">Penerima / Pembayar</label>
                <input type="text" name="recipient_or_payer" class="form-control" placeholder="Nama pihak ketiga atau rekanan">
            </div>
            <div class="col-md-6">
                <label class="form-label small fw-semibold">Nomor SPJ (Bila Ada)</label>
                <input type="text" name="spj_number" class="form-control" placeholder="Contoh: SPJ/01/PBT/2026">
            </div>

            <div class="col-md-12">
                <label class="form-label small fw-semibold">Uraian / Keterangan Transaksi *</label>
                <textarea name="description" class="form-control" rows="3" required placeholder="Jelaskan peruntukan transaksi secara rinci"></textarea>
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
            <a href="{{ route('finance.index') }}" class="btn btn-light px-4">Batal</a>
            <button type="submit" class="btn btn-primary px-4">
                <i class="fa-solid fa-save me-1"></i> Simpan Transaksi Kas
            </button>
        </div>
    </form>
</div>
@endsection
