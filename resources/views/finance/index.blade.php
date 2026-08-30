@extends('layouts.app')

@section('title', 'Keuangan Desa')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1">Keuangan & APBDes</h4>
        <p class="text-muted small mb-0">Pembukuan transaksi kas, penerimaan dana desa, pengeluaran belanja, dan SPJ.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('finance.budgets') }}" class="btn btn-outline-primary btn-sm">
            <i class="fa-solid fa-chart-pie me-1"></i> Anggaran APBDes
        </a>
        <a href="{{ route('finance.create') }}" class="btn btn-primary btn-sm">
            <i class="fa-solid fa-plus me-1"></i> Catat Transaksi Baru
        </a>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card p-3">
            <small class="text-muted fw-semibold">Total Penerimaan Kas</small>
            <h4 class="fw-bold text-success mb-0">Rp {{ number_format($income, 0, ',', '.') }}</h4>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card p-3">
            <small class="text-muted fw-semibold">Total Pengeluaran Kas</small>
            <h4 class="fw-bold text-danger mb-0">Rp {{ number_format($expense, 0, ',', '.') }}</h4>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card p-3">
            <small class="text-muted fw-semibold">Saldo Kas Berjalan</small>
            <h4 class="fw-bold text-primary mb-0">Rp {{ number_format($balance, 0, ',', '.') }}</h4>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="fa-solid fa-list me-2 text-primary"></i>Buku Kas Umum Transaksi</span>
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light small">
                <tr>
                    <th>No. Bukti / Transaksi</th>
                    <th>Tanggal</th>
                    <th>Akun Rekening</th>
                    <th>Uraian Transaksi</th>
                    <th>Metode</th>
                    <th class="text-end">Jumlah</th>
                </tr>
            </thead>
            <tbody class="small">
                @forelse($transactions as $t)
                <tr>
                    <td><code>{{ $t->transaction_number }}</code></td>
                    <td>{{ $t->transaction_date->format('d/m/Y') }}</td>
                    <td><span class="badge bg-light text-dark border">{{ $t->account->name ?? '-' }}</span></td>
                    <td>{{ $t->description }}</td>
                    <td>{{ $t->payment_method }}</td>
                    <td class="text-end fw-bold {{ $t->type == 'PENERIMAAN' ? 'text-success' : 'text-danger' }}">
                        {{ $t->type == 'PENERIMAAN' ? '+' : '-' }} Rp {{ number_format($t->amount, 0, ',', '.') }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center text-muted py-4">Belum ada transaksi kas dicatat.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
