<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>First Run Wizard - SIAP Desa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: #f1f5f9;
            font-family: 'Segoe UI', system-ui, sans-serif;
            min-height: 100vh;
            padding: 2rem 1rem;
        }
        .wizard-container {
            max-width: 800px;
            margin: auto;
            background: #ffffff;
            border-radius: 0.75rem;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        .wizard-header {
            background: #1e3a8a;
            color: #ffffff;
            padding: 2rem;
        }
    </style>
</head>
<body>

<div class="wizard-container">
    <div class="wizard-header text-center">
        <i class="fa-solid fa-landmark fa-3x mb-3"></i>
        <h3 class="fw-bold mb-1">SIAP DESA - Inisialisasi Awal</h3>
        <p class="mb-0 text-white-50">Selamat datang di Sistem Informasi Administrasi Pemerintahan Desa (Local-First)</p>
    </div>

    <form action="{{ route('setup.store') }}" method="POST" class="p-4 p-md-5">
        @csrf
        
        <h5 class="fw-bold text-primary mb-3"><i class="fa-solid fa-house-flag me-2"></i>1. Identitas Pemerintahan Desa</h5>
        <div class="row g-3 mb-4">
            <div class="col-md-8">
                <label class="form-label small fw-semibold">Nama Desa *</label>
                <input type="text" name="village_name" class="form-control" placeholder="Contoh: Desa Sukamaju Sejahtera" required value="{{ old('village_name') }}">
            </div>
            <div class="col-md-4">
                <label class="form-label small fw-semibold">Kode Desa *</label>
                <input type="text" name="village_code" class="form-control" placeholder="Contoh: 32.04.05.2001" required value="{{ old('village_code') }}">
            </div>
            <div class="col-md-4">
                <label class="form-label small fw-semibold">Kecamatan *</label>
                <input type="text" name="district" class="form-control" placeholder="Contoh: Cimenyan" required value="{{ old('district') }}">
            </div>
            <div class="col-md-4">
                <label class="form-label small fw-semibold">Kabupaten / Kota *</label>
                <input type="text" name="regency" class="form-control" placeholder="Contoh: Kabupaten Bandung" required value="{{ old('regency') }}">
            </div>
            <div class="col-md-4">
                <label class="form-label small fw-semibold">Provinsi *</label>
                <input type="text" name="province" class="form-control" placeholder="Contoh: Jawa Barat" required value="{{ old('province') }}">
            </div>
            <div class="col-md-12">
                <label class="form-label small fw-semibold">Nama Kepala Desa (Kades) *</label>
                <input type="text" name="head_name" class="form-control" placeholder="Nama Lengkap Beserta Gelar" required value="{{ old('head_name') }}">
            </div>
        </div>

        <h5 class="fw-bold text-primary mb-3"><i class="fa-solid fa-user-shield me-2"></i>2. Akun Super Administrator</h5>
        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <label class="form-label small fw-semibold">Nama Lengkap Admin *</label>
                <input type="text" name="admin_name" class="form-control" placeholder="Nama Administrator" required value="{{ old('admin_name', 'Administrator Utama') }}">
            </div>
            <div class="col-md-6">
                <label class="form-label small fw-semibold">Username Login *</label>
                <input type="text" name="admin_username" class="form-control" placeholder="admin" required value="{{ old('admin_username', 'admin') }}">
            </div>
            <div class="col-md-6">
                <label class="form-label small fw-semibold">Kata Sandi *</label>
                <input type="password" name="admin_password" class="form-control" placeholder="Minimal 6 karakter" required>
            </div>
            <div class="col-md-6">
                <label class="form-label small fw-semibold">Konfirmasi Kata Sandi *</label>
                <input type="password" name="admin_password_confirmation" class="form-control" placeholder="Ulangi kata sandi" required>
            </div>
        </div>

        <h5 class="fw-bold text-primary mb-3"><i class="fa-solid fa-laptop me-2"></i>3. Identitas Perangkat Lokal (PC)</h5>
        <div class="row g-3 mb-4">
            <div class="col-md-12">
                <label class="form-label small fw-semibold">Nama Komputer / Perangkat Ini *</label>
                <input type="text" name="device_name" class="form-control" placeholder="Contoh: Komputer Pelayanan Meja 1" required value="{{ old('device_name', 'Komputer Utama Kantor Desa') }}">
            </div>
        </div>

        <div class="d-flex justify-content-end">
            <button type="submit" class="btn btn-primary px-4 py-2 fw-semibold">
                <i class="fa-solid fa-check me-2"></i> Selesaikan Inisialisasi & Mulai Aplikasi
            </button>
        </div>
    </form>
</div>

</body>
</html>
