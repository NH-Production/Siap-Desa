@extends('layouts.app')

@section('title', 'Bagan Akun Standar APBDes')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1">Bagan Akun Standar (CoA) APBDes</h4>
        <p class="text-muted small mb-0">Struktur kode rekening pendapatan, belanja, pembiayaan, dan kas desa.</p>
    </div>
    <div class="d-flex gap-2">
        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#newAccountModal">
            <i class="fa-solid fa-plus me-1"></i> Tambah Akun Rekening
        </button>
        <a href="{{ route('finance.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="fa-solid fa-arrow-left me-1"></i> Kembali ke Kas
        </a>
    </div>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light small">
                <tr>
                    <th>Kode Rekening</th>
                    <th>Nama Akun Rekening</th>
                    <th>Kelompok</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody class="small">
                @foreach($accounts as $acc)
                <tr>
                    <td><code>{{ $acc->code }}</code></td>
                    <td class="fw-bold">{{ $acc->name }}</td>
                    <td><span class="badge bg-primary-subtle text-primary border">{{ $acc->type }}</span></td>
                    <td><span class="badge bg-success">Aktif</span></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="newAccountModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('finance.accounts.store') }}" method="POST" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title fs-6 fw-bold">Tambah Akun Rekening APBDes</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Kode Rekening *</label>
                    <input type="text" name="code" class="form-control form-control-sm" required placeholder="Contoh: 4.1.1">
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Nama Akun *</label>
                    <input type="text" name="name" class="form-control form-control-sm" required placeholder="Contoh: Hasil Usaha Desa (BUMDes)">
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Kelompok Akun *</label>
                    <select name="type" class="form-select form-select-sm" required>
                        <option value="PENDAPATAN">PENDAPATAN</option>
                        <option value="BELANJA">BELANJA</option>
                        <option value="PEMBIAYAAN">PEMBIAYAAN</option>
                        <option value="KAS">KAS</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary btn-sm">Simpan Akun</button>
            </div>
        </form>
    </div>
</div>
@endsection
