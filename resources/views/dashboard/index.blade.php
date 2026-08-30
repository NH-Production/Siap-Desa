@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1">Dashboard Pemerintahan Desa</h4>
        <p class="text-muted small mb-0">{{ $village->name ?? 'Desa' }} • {{ $village->district ?? '' }}, {{ $village->regency ?? '' }}</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('letters.create') }}" class="btn btn-primary btn-sm">
            <i class="fa-solid fa-plus me-1"></i> Buat Surat Baru
        </a>
        <a href="{{ route('attendance.scanner') }}" class="btn btn-success btn-sm">
            <i class="fa-solid fa-qrcode me-1"></i> Scan Absensi QR
        </a>
    </div>
</div>

<!-- Primary Stats Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card p-3">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small fw-semibold">Total Penduduk</div>
                    <h3 class="fw-bold mb-0 text-dark">{{ number_format($totalCitizens) }}</h3>
                    <small class="text-muted">Jiwa Terdaftar</small>
                </div>
                <div class="p-3 bg-primary bg-opacity-10 text-primary rounded-3">
                    <i class="fa-solid fa-users fa-2x"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card p-3">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small fw-semibold">Kepala Keluarga (KK)</div>
                    <h3 class="fw-bold mb-0 text-dark">{{ number_format($totalFamilies) }}</h3>
                    <small class="text-muted">Kartu Keluarga</small>
                </div>
                <div class="p-3 bg-info bg-opacity-10 text-info rounded-3">
                    <i class="fa-solid fa-people-roof fa-2x"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card p-3">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small fw-semibold">Absensi Hari Ini</div>
                    <h3 class="fw-bold mb-0 text-success">{{ $todayAttendance }} / {{ $totalEmployees }}</h3>
                    <small class="text-muted">Pegawai Hadir</small>
                </div>
                <div class="p-3 bg-success bg-opacity-10 text-success rounded-3">
                    <i class="fa-solid fa-clipboard-user fa-2x"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card p-3">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small fw-semibold">Saldo Kas Desa</div>
                    <h4 class="fw-bold mb-0 text-primary">Rp {{ number_format($cashBalance, 0, ',', '.') }}</h4>
                    <small class="text-muted">APBDes Berjalan</small>
                </div>
                <div class="p-3 bg-warning bg-opacity-10 text-warning rounded-3">
                    <i class="fa-solid fa-wallet fa-2x"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Secondary Section: Recent Letters & Transactions -->
<div class="row g-4 mb-4">
    <div class="col-md-7">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="fa-solid fa-envelope me-2 text-primary"></i>Pelayanan Surat Terbaru</span>
                <a href="{{ route('letters.index') }}" class="btn btn-sm btn-link p-0 text-decoration-none">Lihat Semua</a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light small">
                        <tr>
                            <th>No. Surat</th>
                            <th>Pemohon</th>
                            <th>Jenis Surat</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody class="small">
                        @forelse($recentLetters as $letter)
                        <tr>
                            <td class="fw-semibold">{{ $letter->letter_number ?? $letter->draft_number }}</td>
                            <td>{{ $letter->applicant_name }}</td>
                            <td>{{ $letter->letterType->name ?? '-' }}</td>
                            <td>
                                @if($letter->status === 'APPROVED')
                                    <span class="badge bg-success">Disetujui</span>
                                @else
                                    <span class="badge bg-warning text-dark">{{ $letter->status }}</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted py-3">Belum ada pengajuan surat terbaru.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-5">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="fa-solid fa-money-bill-transfer me-2 text-success"></i>Kas & Transaksi Terakhir</span>
                <a href="{{ route('finance.index') }}" class="btn btn-sm btn-link p-0 text-decoration-none">Detail Kas</a>
            </div>
            <div class="p-3">
                @forelse($recentTransactions as $trx)
                <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                    <div>
                        <div class="fw-semibold small">{{ $trx->description }}</div>
                        <small class="text-muted">{{ $trx->transaction_date->format('d/m/Y') }} • {{ $trx->type }}</small>
                    </div>
                    <div class="fw-bold {{ $trx->type === 'PENERIMAAN' ? 'text-success' : 'text-danger' }}">
                        {{ $trx->type === 'PENERIMAAN' ? '+' : '-' }} Rp {{ number_format($trx->amount, 0, ',', '.') }}
                    </div>
                </div>
                @empty
                <div class="text-center text-muted py-3 small">Belum ada transaksi kas tercatat.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- System Sync & Offline Status Footer Bar -->
<div class="card bg-light p-3 border">
    <div class="row align-items-center">
        <div class="col-md-8">
            <div class="d-flex align-items-center gap-2">
                <i class="fa-solid fa-shield-halved text-success fs-4"></i>
                <div>
                    <strong>Status Operasional: Mandiri (Local-First Offline)</strong>
                    <div class="text-muted small">Semua data tersimpan aman pada database lokal komputer. Perubahan akan masuk ke Sync Queue.</div>
                </div>
            </div>
        </div>
        <div class="col-md-4 text-md-end mt-2 mt-md-0">
            <span class="badge bg-secondary me-2">Pending Queue: {{ $pendingPushCount }}</span>
            <span class="badge bg-{{ $conflictCount > 0 ? 'danger' : 'success' }}">Konflik: {{ $conflictCount }}</span>
        </div>
    </div>
</div>
@endsection
