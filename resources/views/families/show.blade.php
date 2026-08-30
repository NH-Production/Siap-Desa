@extends('layouts.app')

@section('title', 'Detail KK - ' . $family->head_name)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1">Kartu Keluarga No: {{ $family->no_kk }}</h4>
        <p class="text-muted small mb-0">Kepala Keluarga: <strong>{{ $family->head_name }}</strong> • Alamat: {{ $family->address }} (RT {{ $family->rt }}/RW {{ $family->rw }})</p>
    </div>
    <a href="{{ route('families.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="fa-solid fa-arrow-left me-1"></i> Kembali
    </a>
</div>

<div class="row g-4">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="fa-solid fa-users me-2 text-primary"></i>Daftar Anggota Keluarga</span>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light small">
                        <tr>
                            <th>NIK</th>
                            <th>Nama Anggota</th>
                            <th>Hubungan</th>
                            <th>JK</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="small">
                        @forelse($family->members as $m)
                        <tr>
                            <td><code>{{ $m->citizen->nik ?? '-' }}</code></td>
                            <td class="fw-bold">{{ $m->citizen->name ?? '-' }}</td>
                            <td><span class="badge bg-primary-subtle text-primary border">{{ $m->relation_status }}</span></td>
                            <td>{{ $m->citizen->gender == 'LAKI_LAKI' ? 'L' : 'P' }}</td>
                            <td class="text-end">
                                <form action="{{ route('families.members.remove', $m->uuid) }}" method="POST" class="d-inline" onsubmit="return confirm('Keluarkan anggota ini dari KK?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-link text-danger p-0"><i class="fa-solid fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-3">Belum ada anggota keluarga yang terdaftar.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header"><i class="fa-solid fa-user-plus me-2 text-success"></i>Tambah Anggota Keluarga</div>
            <form action="{{ route('families.members.add', $family->uuid) }}" method="POST" class="card-body">
                @csrf
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Pilih Warga / Penduduk</label>
                    <select name="citizen_id" class="form-select form-select-sm" required>
                        <option value="">-- Pilih Penduduk --</option>
                        @foreach($availableCitizens as $c)
                            <option value="{{ $c->id }}">{{ $c->name }} (NIK: {{ $c->nik }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Hubungan Keluarga</label>
                    <select name="relation_status" class="form-select form-select-sm" required>
                        <option value="KEPALA_KELUARGA">Kepala Keluarga</option>
                        <option value="SUAMI">Suami</option>
                        <option value="ISTRI">Istri</option>
                        <option value="ANAK">Anak</option>
                        <option value="ORANG_TUA">Orang Tua</option>
                        <option value="MERTUA">Mertua</option>
                        <option value="FAMILI_LAIN">Famili Lain</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-success btn-sm w-100">
                    <i class="fa-solid fa-plus me-1"></i> Tambahkan ke KK
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
