@extends('layouts.app')
@section('title','Detail KK')
@section('content')
<div class="d-flex justify-content-between mb-3"><div><h5 class="fw-bold mb-1">Kartu Keluarga</h5><div class="text-muted">{{ $family->no_kk }} — {{ $family->head_name }}</div></div><a href="{{ route('families.edit',$family->uuid) }}" class="btn btn-warning btn-sm">Ubah KK</a></div>
<div class="card mb-3"><div class="card-body"><div class="table-responsive"><table class="table"><thead><tr><th>NIK</th><th>Nama</th><th>Hubungan</th><th>Aksi</th></tr></thead><tbody>
@forelse($family->members as $m)<tr><td>{{ $m->citizen?->nik }}</td><td>{{ $m->citizen?->name }}</td><td>{{ $m->relation_status }}</td><td><form method="POST" action="{{ route('families.members.remove',$m->uuid) }}">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">Keluarkan</button></form></td></tr>@empty<tr><td colspan="4" class="text-center text-muted">Belum ada anggota.</td></tr>@endforelse
</tbody></table></div></div></div>
<div class="card"><div class="card-body"><h6 class="fw-bold">Tambah Anggota</h6><form method="POST" action="{{ route('families.members.add',$family->uuid) }}">@csrf<div class="row g-2"><div class="col-md-8"><select name="citizen_id" class="form-select">@foreach($availableCitizens as $c)<option value="{{ $c->id }}">{{ $c->nik }} — {{ $c->name }}</option>@endforeach</select></div><div class="col-md-4"><select name="relation_status" class="form-select"><option>ISTRI</option><option>SUAMI</option><option>ANAK</option><option>ORANG_TUA</option><option>FAMILI_LAIN</option><option>LAINNYA</option></select></div></div><button class="btn btn-primary mt-3">Tambah Anggota</button></form></div></div>
@endsection
