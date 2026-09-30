@extends('layouts.app')
@section('title','Tambah Penduduk')
@section('content')
<div class="card"><div class="card-body"><h5 class="fw-bold mb-4">Tambah Data Penduduk</h5>
<form method="POST" action="{{ route('citizens.store') }}">@csrf
<div class="row g-3">
@foreach([['nik','NIK','text'],['no_kk','No. KK','text'],['name','Nama Lengkap','text'],['birth_place','Tempat Lahir','text'],['birth_date','Tanggal Lahir','date'],['occupation','Pekerjaan','text'],['education','Pendidikan','text'],['religion','Agama','text'],['marital_status','Status Perkawinan','text'],['address','Alamat','text'],['rt','RT','text'],['rw','RW','text']] as $f)
<div class="col-md-{{ in_array($f[0],['name','address'])?'6':'4' }}"><label class="form-label">{{ $f[1] }}</label><input name="{{ $f[0] }}" type="{{ $f[2] }}" value="{{ old($f[0]) }}" class="form-control @error($f[0]) is-invalid @enderror">@error($f[0])<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
@endforeach
<div class="col-md-4"><label class="form-label">Jenis Kelamin</label><select name="gender" class="form-select"><option value="LAKI_LAKI">Laki-laki</option><option value="PEREMPUAN">Perempuan</option></select></div>
<div class="col-md-4"><label class="form-label">Status Penduduk</label><select name="status" class="form-select"><option>TETAP</option><option>SEMENTARA</option><option>PINDAH</option><option>MENINGGAL</option></select></div>
<div class="col-md-4"><label class="form-label">Golongan Darah</label><input name="blood_type" class="form-control"></div>
<div class="col-12"><label class="form-label">Catatan</label><textarea name="notes" class="form-control"></textarea></div>
</div><div class="mt-4 d-flex gap-2"><button class="btn btn-primary">Simpan</button><a href="{{ route('citizens.index') }}" class="btn btn-light">Batal</a></div>
</form></div></div>
@endsection
