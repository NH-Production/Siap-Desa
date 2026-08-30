<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - SIAP Desa (Sistem Informasi Administrasi Pemerintahan Desa)</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome 6 -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    
    <style>
        :root {
            --siap-primary: #1e3a8a;
            --siap-primary-light: #2563eb;
            --siap-dark: #0f172a;
            --siap-bg: #f8fafc;
            --siap-card: #ffffff;
            --siap-border: #e2e8f0;
        }
        body {
            background-color: var(--siap-bg);
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            color: #334155;
            min-height: 100vh;
        }
        #sidebar {
            width: 260px;
            background: #0f172a;
            color: #94a3b8;
            min-height: 100vh;
            transition: all 0.3s;
        }
        #sidebar .sidebar-brand {
            padding: 1.25rem 1rem;
            background: #090d16;
            color: #ffffff;
            font-weight: 700;
            font-size: 1.15rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            border-bottom: 1px solid #1e293b;
        }
        #sidebar .nav-link {
            color: #94a3b8;
            padding: 0.65rem 1rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-size: 0.88rem;
            font-weight: 500;
            border-radius: 0.375rem;
            margin: 0.15rem 0.65rem;
            transition: all 0.2s;
        }
        #sidebar .nav-link:hover {
            color: #ffffff;
            background: #1e293b;
        }
        #sidebar .nav-link.active {
            color: #ffffff;
            background: var(--siap-primary-light);
            font-weight: 600;
        }
        #sidebar .nav-header {
            font-size: 0.72rem;
            text-transform: uppercase;
            font-weight: 700;
            letter-spacing: 0.05em;
            color: #64748b;
            padding: 0.75rem 1.25rem 0.25rem;
        }
        #main-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            overflow-x: hidden;
        }
        .topbar {
            background: #ffffff;
            border-bottom: 1px solid var(--siap-border);
            padding: 0.75rem 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .content-area {
            padding: 1.5rem;
            flex: 1;
        }
        .card {
            border: 1px solid var(--siap-border);
            border-radius: 0.5rem;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05);
            background: #ffffff;
        }
        .card-header {
            background-color: #ffffff;
            border-bottom: 1px solid var(--siap-border);
            padding: 0.85rem 1.25rem;
            font-weight: 600;
        }
        .badge-status {
            font-size: 0.75rem;
            padding: 0.35em 0.65em;
            border-radius: 9999px;
        }
        .table > :not(caption) > * > * {
            padding: 0.75rem 1rem;
            vertical-align: middle;
        }
        .btn-primary {
            background-color: var(--siap-primary);
            border-color: var(--siap-primary);
        }
        .btn-primary:hover {
            background-color: var(--siap-primary-light);
            border-color: var(--siap-primary-light);
        }
    </style>
    @stack('styles')
</head>
<body>
<div class="d-flex">
    <!-- Sidebar -->
    <nav id="sidebar">
        <div class="sidebar-brand">
            <i class="fa-solid fa-landmark text-primary"></i>
            <div>
                <div style="line-height: 1.1;">SIAP DESA</div>
                <small style="font-size: 0.65rem; color: #64748b; font-weight: 400;">v1.0.0 • Local-First</small>
            </div>
        </div>

        <div class="py-2" style="max-height: calc(100vh - 65px); overflow-y: auto;">
            <div class="nav-header">Utama</div>
            <a class="nav-link {{ request()->is('/') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                <i class="fa-solid fa-gauge-high"></i> Dashboard
            </a>
            <a class="nav-link {{ request()->is('village*') ? 'active' : '' }}" href="{{ route('village.profile') }}">
                <i class="fa-solid fa-house-chimney"></i> Profil & Wilayah
            </a>

            <div class="nav-header">Kependudukan</div>
            <a class="nav-link {{ request()->is('citizens*') ? 'active' : '' }}" href="{{ route('citizens.index') }}">
                <i class="fa-solid fa-users"></i> Data Penduduk
            </a>
            <a class="nav-link {{ request()->is('families*') ? 'active' : '' }}" href="{{ route('families.index') }}">
                <i class="fa-solid fa-people-roof"></i> Kartu Keluarga (KK)
            </a>

            <div class="nav-header">Kepegawaian & Pelayanan</div>
            <a class="nav-link {{ request()->is('employees*') ? 'active' : '' }}" href="{{ route('employees.index') }}">
                <i class="fa-solid fa-user-tie"></i> Aparat & Pegawai
            </a>
            <a class="nav-link {{ request()->is('attendance*') ? 'active' : '' }}" href="{{ route('attendance.index') }}">
                <i class="fa-solid fa-qrcode"></i> Absensi QR Code
            </a>
            <a class="nav-link {{ request()->is('letters*') ? 'active' : '' }}" href="{{ route('letters.index') }}">
                <i class="fa-solid fa-envelope-open-text"></i> Administrasi Surat
            </a>
            <a class="nav-link {{ request()->is('services*') ? 'active' : '' }}" href="{{ route('services.index') }}">
                <i class="fa-solid fa-hand-holding-heart"></i> Pelayanan Publik
            </a>

            <div class="nav-header">Keuangan & Aset</div>
            <a class="nav-link {{ request()->is('finance*') ? 'active' : '' }}" href="{{ route('finance.index') }}">
                <i class="fa-solid fa-money-bill-wave"></i> Keuangan & APBDes
            </a>
            <a class="nav-link {{ request()->is('assets*') ? 'active' : '' }}" href="{{ route('assets.index') }}">
                <i class="fa-solid fa-boxes-stacked"></i> Aset & Inventaris
            </a>
            <a class="nav-link {{ request()->is('aid*') ? 'active' : '' }}" href="{{ route('aid.index') }}">
                <i class="fa-solid fa-gift"></i> Bantuan Sosial
            </a>

            <div class="nav-header">Laporan & Sinkronisasi</div>
            <a class="nav-link {{ request()->is('reports*') ? 'active' : '' }}" href="{{ route('reports.index') }}">
                <i class="fa-solid fa-file-invoice"></i> Pusat Laporan
            </a>
            <a class="nav-link {{ request()->is('sync*') ? 'active' : '' }}" href="{{ route('sync.index') }}">
                <i class="fa-solid fa-rotate"></i> Sync & Conflict Center
            </a>
            <a class="nav-link {{ request()->is('backup*') ? 'active' : '' }}" href="{{ route('backup.index') }}">
                <i class="fa-solid fa-database"></i> Backup & Restore
            </a>

            <div class="nav-header">Pengaturan & Pembaruan</div>
            <a class="nav-link {{ request()->is('system/settings*') ? 'active' : '' }}" href="{{ route('system.settings') }}">
                <i class="fa-solid fa-server"></i> Pengaturan Server
            </a>
            <a class="nav-link {{ request()->is('system/updates*') ? 'active' : '' }}" href="{{ route('system.updates') }}">
                <i class="fa-solid fa-cloud-arrow-down"></i> Update & Patch
            </a>
            <a class="nav-link {{ request()->is('system/diagnostics*') || request()->is('system/audit*') ? 'active' : '' }}" href="{{ route('system.diagnostics') }}">
                <i class="fa-solid fa-heart-pulse"></i> Diagnostik & Audit
            </a>
        </div>
    </nav>

    <!-- Main Content -->
    <div id="main-content">
        <!-- Topbar -->
        <header class="topbar">
            <div class="d-flex align-items-center gap-3">
                <span class="badge bg-success badge-status">
                    <i class="fa-solid fa-circle me-1" style="font-size: 0.55rem;"></i> LOCAL-FIRST (OFFLINE READY)
                </span>
                <span class="text-muted small">
                    <i class="fa-solid fa-laptop me-1"></i> {{ session('device_name', 'Local Windows PC') }}
                </span>
            </div>

            <div class="d-flex align-items-center gap-3">
                <a href="{{ route('system.settings') }}" class="btn btn-sm btn-outline-primary">
                    <i class="fa-solid fa-server me-1"></i> Server Cloud
                </a>
                <a href="{{ route('sync.index') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="fa-solid fa-rotate me-1 text-primary"></i> Sync Center
                </a>

                <div class="dropdown">
                    <button class="btn btn-sm btn-light dropdown-toggle d-flex align-items-center gap-2 border" type="button" data-bs-toggle="dropdown">
                        <i class="fa-solid fa-user-circle text-primary"></i>
                        <span>{{ Auth::user()->name ?? 'User' }}</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                        <li>
                            <div class="dropdown-item-text">
                                <small class="text-muted d-block">Masuk sebagai:</small>
                                <strong>{{ Auth::user()->username ?? 'admin' }}</strong>
                            </div>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item" href="{{ route('system.settings') }}">
                                <i class="fa-solid fa-server me-2 text-primary"></i> Pengaturan Server
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('system.updates') }}">
                                <i class="fa-solid fa-cloud-arrow-down me-2 text-success"></i> Update & Patch Center
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('village.profile') }}">
                                <i class="fa-solid fa-cog me-2"></i> Pengaturan Desa
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('system.diagnostics') }}">
                                <i class="fa-solid fa-stethoscope me-2"></i> Diagnostik Sistem
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger">
                                    <i class="fa-solid fa-sign-out-alt me-2"></i> Keluar
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </header>

        <!-- Flash Messages -->
        <div class="px-4 pt-3">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show d-flex align-items-center" role="alert">
                    <i class="fa-solid fa-circle-check me-2 fs-5"></i>
                    <div>{{ session('success') }}</div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center" role="alert">
                    <i class="fa-solid fa-triangle-exclamation me-2 fs-5"></i>
                    <div>{{ session('error') }}</div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('info'))
                <div class="alert alert-info alert-dismissible fade show d-flex align-items-center" role="alert">
                    <i class="fa-solid fa-circle-info me-2 fs-5"></i>
                    <div>{{ session('info') }}</div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
        </div>

        <!-- Content Area -->
        <main class="content-area">
            @yield('content')
        </main>
    </div>
</div>

<!-- Bootstrap 5 JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>
