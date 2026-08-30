<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Central Cloud Server Panel') - SIAP Desa Cloud</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="icon" href="{{ asset('siap_desa.ico') }}" type="image/x-icon">
    <style>
        :root {
            --central-navy: #0f172a;
            --central-sidebar: #1e1b4b; /* Deep Indigo */
            --central-primary: #6366f1;
            --central-primary-hover: #4f46e5;
            --central-bg: #f8fafc;
        }
        body {
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            background-color: var(--central-bg);
            color: #334155;
        }
        #sidebar {
            width: 260px;
            min-height: 100vh;
            background: var(--central-sidebar);
            color: #fff;
            position: fixed;
            top: 0; left: 0; bottom: 0;
            z-index: 1000;
        }
        .sidebar-brand {
            padding: 1.25rem 1.5rem;
            font-size: 1.15rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        .nav-header {
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #94a3b8;
            padding: 1rem 1.5rem 0.35rem;
            font-weight: 700;
        }
        .nav-link {
            color: #cbd5e1;
            padding: 0.65rem 1.5rem;
            font-size: 0.88rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            transition: all 0.15s ease-in-out;
        }
        .nav-link:hover, .nav-link.active {
            color: #ffffff;
            background-color: rgba(99, 102, 241, 0.25);
            border-left: 4px solid var(--central-primary);
        }
        .nav-link i { width: 1.25rem; text-align: center; }
        #main-content {
            margin-left: 260px;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .topbar {
            height: 60px;
            background: #ffffff;
            border-bottom: 1px solid #e2e8f0;
            padding: 0 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .content-body {
            padding: 1.5rem;
            flex: 1;
        }
        .card {
            border: 1px solid #e2e8f0;
            border-radius: 0.75rem;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05);
        }
        .card-header {
            background-color: #ffffff;
            border-bottom: 1px solid #e2e8f0;
            font-weight: 600;
            padding: 1rem 1.25rem;
        }
    </style>
</head>
<body>
<div class="d-flex">
    <!-- Central Sidebar -->
    <nav id="sidebar">
        <div class="sidebar-brand">
            <i class="fa-solid fa-server text-warning"></i>
            <div>
                <div style="line-height: 1.1;">SIAP CLOUD</div>
                <small style="font-size: 0.65rem; color: #a5b4fc; font-weight: 400;">Central SAAS Portal</small>
            </div>
        </div>

        <div class="py-2">
            <div class="nav-header">Pusat Kontrol</div>
            <a class="nav-link {{ request()->is('central') ? 'active' : '' }}" href="{{ route('central.dashboard') }}">
                <i class="fa-solid fa-gauge-high"></i> Dashboard Server
            </a>
            <a class="nav-link {{ request()->is('central/villages*') ? 'active' : '' }}" href="{{ route('central.villages.index') }}">
                <i class="fa-solid fa-tree-city"></i> Manajemen Desa
            </a>
            <a class="nav-link {{ request()->is('central/licenses*') ? 'active' : '' }}" href="{{ route('central.licenses.index') }}">
                <i class="fa-solid fa-key"></i> Manajemen Lisensi
            </a>

            <div class="nav-header">Distribusi & Sinkronisasi</div>
            <a class="nav-link {{ request()->is('central/releases*') ? 'active' : '' }}" href="{{ route('central.releases.index') }}">
                <i class="fa-solid fa-cloud-arrow-up"></i> Rilis & Patch OTA
            </a>
            <a class="nav-link {{ request()->is('central/telemetry*') ? 'active' : '' }}" href="{{ route('central.telemetry') }}">
                <i class="fa-solid fa-satellite-dish"></i> Live Telemetri Sync
            </a>

            <div class="nav-header">Klien & Navigasi</div>
            <a class="nav-link" href="{{ route('dashboard') }}" target="_blank">
                <i class="fa-solid fa-arrow-up-right-from-square"></i> Buka Aplikasi Klien
            </a>
        </div>
    </nav>

    <!-- Main Content Area -->
    <div id="main-content">
        <header class="topbar">
            <div class="d-flex align-items-center gap-3">
                <span class="badge bg-indigo text-white" style="background-color: #4f46e5;">
                    <i class="fa-solid fa-circle me-1 text-success" style="font-size: 0.55rem;"></i> MASTER SERVER ONLINE
                </span>
                <span class="text-muted small">
                    <i class="fa-solid fa-network-wired me-1"></i> Port: 8090 • API Gateway Active
                </span>
            </div>

            <div class="d-flex align-items-center gap-3">
                <span class="badge bg-light text-dark border">
                    <i class="fa-solid fa-shield-halved text-primary me-1"></i> SAAS Enterprise Engine
                </span>
                <div class="dropdown">
                    <button class="btn btn-sm btn-light border dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <i class="fa-solid fa-user-shield text-indigo me-1"></i> Master Admin
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                        <li><a class="dropdown-item" href="{{ route('central.dashboard') }}"><i class="fa-solid fa-server me-2"></i>Dashboard Pusat</a></li>
                        <li><a class="dropdown-item" href="{{ route('central.releases.index') }}"><i class="fa-solid fa-cloud-arrow-up me-2"></i>Pusat Patch OTA</a></li>
                    </ul>
                </div>
            </div>
        </header>

        <div class="content-body">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fa-solid fa-circle-check me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fa-solid fa-circle-exclamation me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @yield('content')
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>
