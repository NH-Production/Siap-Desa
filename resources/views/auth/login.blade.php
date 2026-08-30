<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - SIAP Desa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', system-ui, sans-serif;
        }
        .login-card {
            background: #ffffff;
            border-radius: 1rem;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            width: 100%;
            max-width: 440px;
            overflow: hidden;
        }
        .login-header {
            background: #f8fafc;
            padding: 2rem 2rem 1.5rem;
            text-align: center;
            border-bottom: 1px solid #e2e8f0;
        }
    </style>
</head>
<body>

<div class="login-card">
    <div class="login-header">
        <div class="d-inline-flex p-3 rounded-circle bg-primary bg-opacity-10 text-primary mb-3">
            <i class="fa-solid fa-landmark fa-2x"></i>
        </div>
        <h4 class="fw-bold mb-1">SIAP DESA</h4>
        <p class="text-muted small mb-0">{{ $village->name ?? 'Sistem Informasi Administrasi Pemerintahan Desa' }}</p>
    </div>

    <div class="p-4">
        @if(session('error'))
            <div class="alert alert-danger small py-2">{{ session('error') }}</div>
        @endif
        @if(session('info'))
            <div class="alert alert-info small py-2">{{ session('info') }}</div>
        @endif
        @if(session('success'))
            <div class="alert alert-success small py-2">{{ session('success') }}</div>
        @endif

        <form action="{{ route('login.submit') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label small fw-semibold">Username Pengguna</label>
                <div class="input-group">
                    <span class="input-group-text bg-light"><i class="fa-solid fa-user text-muted"></i></span>
                    <input type="text" name="username" class="form-control" placeholder="Masukkan username" required autofocus value="{{ old('username', 'admin') }}">
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label small fw-semibold">Kata Sandi</label>
                <div class="input-group">
                    <span class="input-group-text bg-light"><i class="fa-solid fa-lock text-muted"></i></span>
                    <input type="password" name="password" class="form-control" placeholder="Masukkan kata sandi" required value="admin123">
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center mb-4">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="remember" id="remember" checked>
                    <label class="form-check-label small text-muted" for="remember">Ingat Saya</label>
                </div>
                <span class="badge bg-success-subtle text-success border border-success-subtle">
                    <i class="fa-solid fa-circle me-1" style="font-size: 0.5rem;"></i> OFFLINE READY
                </span>
            </div>

            <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold">
                <i class="fa-solid fa-arrow-right-to-bracket me-1"></i> Masuk ke Sistem
            </button>
        </form>

        <div class="mt-4 pt-3 border-top text-center text-muted small">
            <div>Perangkat: <strong>{{ $device->device_code ?? 'PC-ADMIN' }}</strong></div>
            <div class="text-secondary" style="font-size: 0.72rem;">SIAP Desa v1.0.0 • Local-First Architecture</div>
        </div>
    </div>
</div>

</body>
</html>
