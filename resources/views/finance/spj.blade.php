@extends('layouts.app')

@section('title', 'Laporan SPJ Keuangan')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1">Surat Pertanggungjawaban (SPJ)</h4>
        <p class="text-muted small mb-0">Daftar transaksi belanja desa yang telah memiliki nomor SPJ.</p>
    </div>
    <a href="{{ route('finance.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="fa-solid fa-arrow-left me-1"></i> Kembali ke Kas
    </a>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light small">
                <tr>
                    <th>Nomor SPJ</th>
                    <th>Nomor Transaksi</th>
                    <th>Tanggal</th>
                    <th>Uraian Belanja</th>
                    <th>Penerima</th>
                    <th class="text-end">Jumlah Realisasi</th>
                </tr>
            </thead>
            <tbody class="small">
                @forelse($transactions as $t)
                <tr>
                    <td class="fw-bold"><code>{{ $t->spj_number }}</code></td>
                    <td>{{ $t->transaction_number }}</td>
                    <td>{{ $t->transaction_date->format('d/m/Y') }}</td>
                    <td>{{ $t->description }}</td>
                    <td>{{ $t->recipient_or_payer ?? '-' }}</td>
                    <td class="text-end fw-bold text-danger">Rp {{ number_format($t->amount, 0, ',', '.') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center text-muted py-4">Belum ada transaksi SPJ tercatat.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
