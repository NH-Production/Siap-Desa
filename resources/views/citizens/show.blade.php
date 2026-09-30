@extends('layouts.app')
@section('title','Detail Penduduk')
@section('content')
<div class="d-flex justify-content-between mb-3"><h5 class="fw-bold">Detail Penduduk</h5><a href="{{ route('citizens.edit',$citizen->uuid) }}" class="btn btn-warning btn-sm">Ubah</a></div>
<div class="card"><div class="card-body"><div class="row g-3">
@foreach(['nik'=>'NIK','no_kk'=>'No. KK','name'=>'Nama Lengkap','gender'=>'Jenis Kelamin','birth_place'=>'Tempat Lahir','birth_date'=>'Tanggal Lahir','religion'=>'Agama','marital_status'=>'Status Perkawinan','occupation'=>'Pekerjaan','education'=>'Pendidikan','address'=>'Alamat','rt'=>'RT','rw'=>'RW','status'=>'Status'] as $k=>$label)
<div class="col-md-4"><small class="text-muted">{{ $label }}</small><div class="fw-semibold">{{ $citizen->{$k} instanceof \Carbon\Carbon ? $citizen->{$k}->format('d-m-Y') : ($citizen->{$k} ?: '-') }}</div></div>
@endforeach
</div></div></div>
@endsection
