@extends('layouts.central')

@section('title', 'Pusat Rilis & Patch OTA')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div><h4 class="fw-bold mb-1">Pusat Rilis & Patch</h4><p class="text-muted small mb-0">Kelola paket update desktop SIAP-DESA.</p></div>
</div>
@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
<div class="card">
<div class="table-responsive"><table class="table table-hover mb-0 align-middle">
<thead><tr><th>Versi</th><th>Minimum</th><th>Schema</th><th>Sync</th><th>File</th><th>SHA-256</th><th>Status</th></tr></thead>
<tbody>
@forelse($releases as $rel)
<tr>
<td><span class="badge bg-primary">v{{ $rel->version }}</span><div class="small">{{ $rel->title }}</div></td>
<td>{{ $rel->minimum_version }}</td><td>{{ $rel->schema_version }}</td><td>{{ $rel->sync_protocol }}</td>
<td>{{ $rel->file_name ?: '-' }}<br><small>{{ $rel->file_size ? number_format($rel->file_size/1048576,2).' MB' : '-' }}</small></td>
<td><code class="small">{{ $rel->checksum_sha256 ?: '-' }}</code></td>
<td>{!! $rel->is_mandatory ? '<span class="badge bg-danger">MANDATORY</span>' : '<span class="badge bg-success">OPTIONAL</span>' !!}</td>
</tr>
@empty <tr><td colspan="7" class="text-center py-4 text-muted">Belum ada rilis.</td></tr>@endforelse
</tbody></table></div>
<div class="card-footer">{{ $releases->links() }}</div></div>

<div class="card mt-4">
<div class="card-body">
<form action="{{ route('central.releases.store') }}" method="POST" enctype="multipart/form-data" class="row g-3">
@csrf
<div class="col-md-2"><label class="form-label">Version</label><input name="version" class="form-control" placeholder="1.0.1" required></div>
<div class="col-md-2"><label class="form-label">Minimum</label><input name="minimum_version" class="form-control" placeholder="1.0.0" required></div>
<div class="col-md-2"><label class="form-label">Schema</label><input name="schema_version" type="number" class="form-control" value="1" required></div>
<div class="col-md-2"><label class="form-label">Sync Protocol</label><input name="sync_protocol" type="number" class="form-control" value="1" required></div>
<div class="col-md-4"><label class="form-label">Judul</label><input name="title" class="form-control" required></div>
<div class="col-md-8"><label class="form-label">Changelog</label><textarea name="changelog" class="form-control" rows="2" required></textarea></div>
<div class="col-md-4"><label class="form-label">Patch ZIP</label><input name="patch_file" type="file" accept=".zip" class="form-control" required></div>
<div class="col-12 form-check ms-2"><input name="is_mandatory" value="1" type="checkbox" class="form-check-input" id="mandatory"><label for="mandatory" class="form-check-label">Mandatory Update</label></div>
<div class="col-12"><button class="btn btn-primary">Publikasikan Patch</button></div>
</form></div></div>
@endsection
