@extends('layouts.app')

@section('title', 'Anggaran APBDes')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1">Rencana Anggaran Pendapatan & Belanja Desa (APBDes)</h4>
        <p class="text-muted small mb-0">Tahun Anggaran: <strong>{{ $year }}</strong></p>
    </div>
    <a href="{{ route('finance.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="fa-solid fa-arrow-left me-1"></i> Kembali ke Kas
    </a>
</div>

<form action="{{ route('finance.budgets.store') }}" method="POST" class="card">
    @csrf
    <input type="hidden" name="fiscal_year" value="{{ $year }}">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light small">
                <tr>
                    <th>Kode Rekening</th>
                    <th>Uraian Akun</th>
                    <th>Kelompok</th>
                    <th style="width: 250px;">Pagu Anggaran (Rp)</th>
                </tr>
            </thead>
            <tbody class="small">
                @foreach($accounts as $acc)
                <tr>
                    <td><code>{{ $acc->code }}</code></td>
                    <td class="fw-bold">{{ $acc->name }}</td>
                    <td><span class="badge bg-light text-dark border">{{ $acc->type }}</span></td>
                    <td>
                        <input type="number" step="0.01" name="budgets[{{ $acc->id }}]" class="form-control form-control-sm text-end" value="{{ $budgets[$acc->id]->budgeted_amount ?? 0 }}">
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="card-footer bg-white d-flex justify-content-end py-3">
        <button type="submit" class="btn btn-primary btn-sm px-4">
            <i class="fa-solid fa-save me-1"></i> Simpan Pagu Anggaran APBDes
        </button>
    </div>
</form>
@endsection
