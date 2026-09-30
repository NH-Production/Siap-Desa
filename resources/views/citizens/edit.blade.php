@extends('layouts.app')
@section('title','Ubah Penduduk')
@section('content')
<div class="card"><div class="card-body"><h5 class="fw-bold mb-4">Ubah Data Penduduk</h5>
<form method="POST" action="{{ route('citizens.update',$citizen->uuid) }}">@csrf @method('PUT')
<div class="row g-3">
@foreach([['nik','NIK'],['no_kk','No. KK'],['name','Nama Lengkap'],['birth_place','Tempat Lahir'],['occupation','Pekerjaan'],['education','Pendidikan'],['religion','Agama'],['marital_status','Status Perkawinan'],['address','Alamat'],['rt','RT'],['rw','RW'],['blood_type','Golongan Darah']] as $f)
<div class="col-md-4"><label class="form-label">{{ $f[1] }}</label><input name="{{ $f[0] }}" value="{{ old($f[0],$citizen->{$f[0]}) }}" class="form-control"></div>
@endforeach
<div class="col-md-4"><label class="form-label">Tanggal Lahir</label><input type="date" name="birth_date" value="{{ old('birth_date',$citizen->birth_date?->format('Y-m-d')) }}" class="form-control"></div>
<div class="col-md-4"><label class="form-label">Jenis Kelamin</label><select name="gender" class="form-select"><option value="LAKI_LAKI" @selected($citizen->gender==='LAKI_LAKI')>Laki-laki</option><option value="PEREMPUAN" @selected($citizen->gender==='PEREMPUAN')>Perempuan</option></select></div>
<div class="col-md-4"><label class="form-label">Status</label><select name="status" class="form-select">@foreach(['TETAP','SEMENTARA','PINDAH','MENINGGAL'] as $s)<option @selected($citizen->status===$s)>{{ $s }}</option>@endforeach</select></div>
</div><div class="mt-4 d-flex gap-2"><button class="btn btn-primary">Simpan Perubahan</button><a href="{{ route('citizens.show',$citizen->uuid) }}" class="btn btn-light">Batal</a></div>
</form></div></div>
@endsection
