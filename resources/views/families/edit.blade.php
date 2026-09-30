@extends('layouts.app')
@section('title','Ubah KK')
@section('content')
<div class="card"><div class="card-body"><h5 class="fw-bold mb-4">Ubah Kartu Keluarga</h5>
<form method="POST" action="{{ route('families.update',$family->uuid) }}">@csrf @method('PUT')<div class="row g-3">
@foreach([['no_kk','Nomor KK'],['head_name','Nama Kepala Keluarga'],['head_nik','NIK Kepala Keluarga'],['address','Alamat'],['rt','RT'],['rw','RW'],['postal_code','Kode Pos']] as $f)
<div class="col-md-4"><label class="form-label">{{ $f[1] }}</label><input name="{{ $f[0] }}" class="form-control" value="{{ old($f[0],$family->{$f[0]}) }}"></div>
@endforeach
</div><div class="mt-4"><button class="btn btn-primary">Simpan</button></div></form></div></div>
@endsection
