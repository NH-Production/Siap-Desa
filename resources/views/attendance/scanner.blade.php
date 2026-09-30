@extends('layouts.app')
@section('title','Scanner Absensi QR')
@section('content')
<div class="row justify-content-center"><div class="col-lg-8"><div class="card"><div class="card-body text-center"><h4 class="fw-bold">Scanner Absensi QR</h4><p class="text-muted">Arahkan QR kartu pegawai ke kamera. Sistem tetap bekerja tanpa internet.</p><div id="reader" style="max-width:500px;margin:auto"></div><div id="result" class="alert alert-light mt-3">Menunggu QR...</div></div></div></div></div>
@endsection
@push('scripts')
<script src="https://unpkg.com/html5-qrcode"></script>
<script>
const result=document.getElementById('result'); let busy=false;
async function submitToken(token){
 if(busy)return; busy=true;
 try{const r=await fetch('{{ route("attendance.scan.submit") }}',{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}','Accept':'application/json'},body:JSON.stringify({qr_token:token})});const d=await r.json();result.className='alert alert-'+(d.success?'success':'danger');result.textContent=d.message||'Selesai';if(d.success){const a=new AudioContext();const o=a.createOscillator();o.connect(a.destination);o.start();o.stop(a.currentTime+.12)}}catch(e){result.className='alert alert-danger';result.textContent='Gagal memproses QR.'}finally{setTimeout(()=>busy=false,1200)}
}
new Html5QrcodeScanner('reader',{fps:10,qrbox:220},false).render((text)=>submitToken(text),()=>{});
</script>
@endpush