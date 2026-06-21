<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SISWA-TER') | Sistem Warkah Terintegrasi</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <style>
        :root {
            --biru-tua:   #1a3a6b;
            --biru:       #2563eb;
            --biru-muda:  #dbeafe;
            --coklat:     #6b3a2a;
            --coklat-tua: #4a2519;
            --emas:       #b8860b;
            --emas-muda:  #fef3c7;
            --hijau:      #166534;
            --hijau-muda: #dcfce7;
            --merah:      #dc2626;
            --merah-muda: #fee2e2;
            --abu:        #6b7280;
            --abu-muda:   #f3f4f6;
            --sidebar-w:  260px;
        }
        body { background: #f0f4f8; font-family: 'Segoe UI', sans-serif; }

        /* SIDEBAR */
        #sidebar {
            width: var(--sidebar-w);
            min-height: 100vh;
            background: linear-gradient(180deg, var(--biru-tua) 0%, var(--coklat-tua) 100%);
            position: fixed; top: 0; left: 0; z-index: 1000;
            transition: all .3s;
        }
        #sidebar .brand {
            padding: 1.5rem 1rem;
            border-bottom: 1px solid rgba(255,255,255,.15);
            text-align: center;
        }
        #sidebar .brand-logo {
            width: 60px; height: 60px;
            background: var(--emas);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.8rem; color: var(--biru-tua);
            margin: 0 auto .5rem;
            box-shadow: 0 0 0 4px rgba(184,134,11,.25);
        }
        #sidebar .brand h6 { color: #fff; font-weight: 700; font-size: .8rem; margin: 0; }
        #sidebar .brand small { color: var(--emas); font-size: .7rem; }

        .sidebar-nav .nav-item { margin: 2px 8px; }
        .sidebar-nav .nav-link {
            color: rgba(255,255,255,.8);
            padding: .6rem .9rem;
            border-radius: 8px;
            font-size: .875rem;
            transition: all .2s;
            display: flex; align-items: center; gap: 10px;
        }
        .sidebar-nav .nav-link:hover, .sidebar-nav .nav-link.active {
            background: rgba(255,255,255,.15);
            color: #fff;
        }
        .sidebar-nav .nav-link.active { background: var(--emas); color: var(--biru-tua); font-weight: 600; }
        .sidebar-nav .nav-link i { font-size: 1.1rem; width: 20px; text-align: center; }
        .sidebar-label {
            color: rgba(255,255,255,.4);
            font-size: .65rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            padding: .75rem 1.2rem .25rem;
            font-weight: 600;
        }
        .sidebar-user {
            position: absolute; bottom: 0; left: 0; right: 0;
            padding: 1rem;
            border-top: 1px solid rgba(255,255,255,.15);
            background: rgba(0,0,0,.15);
        }
        .sidebar-user .user-avatar {
            width: 36px; height: 36px;
            background: var(--emas);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-weight: 700; color: var(--biru-tua); font-size: .9rem;
        }
        .sidebar-user .user-name { color: #fff; font-size: .8rem; font-weight: 600; }
        .sidebar-user .user-role { color: var(--emas); font-size: .7rem; }

        /* MAIN CONTENT */
        #main-content {
            margin-left: var(--sidebar-w);
            min-height: 100vh;
            transition: all .3s;
        }
        .topbar {
            background: #fff;
            padding: .75rem 1.5rem;
            border-bottom: 2px solid var(--biru-muda);
            display: flex; align-items: center; justify-content: space-between;
            position: sticky; top: 0; z-index: 900;
            box-shadow: 0 2px 8px rgba(0,0,0,.06);
        }
        .topbar .page-title { font-weight: 700; color: var(--biru-tua); margin: 0; font-size: 1.1rem; }
        .topbar .breadcrumb { margin: 0; font-size: .8rem; }
        .content-wrapper { padding: 1.5rem; }

        /* CARDS */
        .stat-card {
            border: none;
            border-radius: 12px;
            padding: 1.25rem;
            box-shadow: 0 2px 12px rgba(0,0,0,.08);
            transition: transform .2s;
        }
        .stat-card:hover { transform: translateY(-2px); }
        .stat-card .stat-icon {
            width: 48px; height: 48px;
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.4rem;
        }
        .stat-card .stat-num { font-size: 2rem; font-weight: 700; }
        .stat-card .stat-label { font-size: .78rem; color: var(--abu); }

        .card-siswa { border: none; border-radius: 12px; box-shadow: 0 2px 12px rgba(0,0,0,.07); }
        .card-siswa .card-header {
            background: var(--biru-tua);
            color: #fff; border-radius: 12px 12px 0 0 !important;
            font-weight: 600; padding: .875rem 1.25rem;
        }

        /* STATUS BADGES */
        .badge-status {
            padding: .35em .7em; border-radius: 20px;
            font-size: .72rem; font-weight: 600;
        }
        .status-menunggu_tu    { background: var(--emas-muda); color: var(--emas); }
        .status-disetujui_tu   { background: var(--biru-muda); color: var(--biru-tua); }
        .status-ditolak_tu     { background: var(--merah-muda); color: var(--merah); }
        .status-diproses_phpt  { background: #e0e7ff; color: #3730a3; }
        .status-warkah_tersedia{ background: var(--hijau-muda); color: var(--hijau); }
        .status-dikembalikan   { background: #f3f4f6; color: var(--abu); }
        .status-selesai        { background: #1f2937; color: #fff; }

        /* TIMELINE */
        .timeline { position: relative; padding-left: 2rem; }
        .timeline::before {
            content: ''; position: absolute; left: .6rem; top: 0; bottom: 0;
            width: 2px; background: #e5e7eb;
        }
        .timeline-item { position: relative; margin-bottom: 1rem; }
        .timeline-dot {
            position: absolute; left: -1.4rem; top: .3rem;
            width: 12px; height: 12px;
            border-radius: 50%; border: 2px solid var(--biru);
            background: #fff;
        }
        .timeline-dot.active { background: var(--biru); }

        /* UPLOAD AREA */
        .upload-area {
            border: 2px dashed var(--biru);
            border-radius: 12px;
            padding: 2rem;
            text-align: center;
            cursor: pointer;
            transition: all .2s;
            background: var(--biru-muda);
        }
        .upload-area:hover { border-color: var(--emas); background: var(--emas-muda); }

        /* OVERDUE INDICATOR */
        .overdue-badge {
            background: var(--merah); color: #fff;
            padding: .2em .5em; border-radius: 4px;
            font-size: .65rem; font-weight: 700;
            animation: pulse 1.5s infinite;
        }
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: .6; }
        }

        /* ROLE COLORS */
        .role-admin { background: #4f46e5; }
        .role-pps   { background: var(--biru-tua); }
        .role-phpt  { background: var(--coklat); }
        .role-tu    { background: var(--hijau); }

        @media (max-width: 768px) {
            #sidebar { width: 0; overflow: hidden; }
            #sidebar.show { width: var(--sidebar-w); }
            #main-content { margin-left: 0; }
        }
    </style>
    @stack('styles')
</head>
<body>
    <!-- SIDEBAR -->
    <nav id="sidebar">
        <div class="brand">
            <div class="brand-logo"><i class="bi bi-archive-fill"></i></div>
            <h6>SISWA-TER</h6>
            <small>Sistem Warkah Terintegrasi</small>
        </div>

        <ul class="nav flex-column sidebar-nav mt-2 pb-5">
            <li class="sidebar-label">MENU UTAMA</li>

            <li class="nav-item">
                <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="bi bi-speedometer2"></i> Dashboard
                </a>
            </li>

            @if(auth()->user()->isPPS())
            <li class="nav-item">
                <a href="{{ route('permintaan.create') }}" class="nav-link {{ request()->routeIs('permintaan.create') ? 'active' : '' }}">
                    <i class="bi bi-plus-circle-fill"></i> Buat Permintaan
                </a>
            </li>
            @endif

            <li class="nav-item">
                <a href="{{ route('permintaan.index') }}" class="nav-link {{ request()->routeIs('permintaan.*') && !request()->routeIs('permintaan.create') ? 'active' : '' }}">
                    <i class="bi bi-folder2-open"></i>
                    {{ auth()->user()->isTU() ? 'Kelola Permintaan' : (auth()->user()->isPHPT() ? 'Upload Warkah' : 'Permintaan Saya') }}
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ route('monitoring.index') }}" class="nav-link {{ request()->routeIs('monitoring.*') ? 'active' : '' }}">
                    <i class="bi bi-bar-chart-line-fill"></i> Monitoring
                </a>
            </li>

            @if(auth()->user()->isAdmin())
            <li class="sidebar-label">ADMINISTRASI</li>
            <li class="nav-item">
                <a href="{{ route('admin.users') }}" class="nav-link {{ request()->routeIs('admin.*') ? 'active' : '' }}">
                    <i class="bi bi-people-fill"></i> Kelola Pengguna
                </a>
            </li>
            @endif
        </ul>

        <div class="sidebar-user">
            <div class="d-flex align-items-center gap-2">
                <div class="user-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
                <div class="flex-1" style="min-width:0">
                    <div class="user-name text-truncate">{{ auth()->user()->name }}</div>
                    <div class="user-role">{{ auth()->user()->role_label }}</div>
                </div>
                <form action="{{ route('logout') }}" method="POST" class="ms-auto">
                    @csrf
                    <button type="submit" class="btn btn-sm p-0" style="color:rgba(255,255,255,.6)" title="Keluar">
                        <i class="bi bi-box-arrow-right fs-5"></i>
                    </button>
                </form>
            </div>
        </div>
    </nav>

    <!-- MAIN CONTENT -->
    <div id="main-content">
        <!-- TOPBAR -->
        <div class="topbar">
            <div>
                <div class="page-title">@yield('page-title', 'Dashboard')</div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Beranda</a></li>
                        @yield('breadcrumb')
                    </ol>
                </nav>
            </div>
            <div class="d-flex align-items-center gap-3">
                <span class="badge text-white role-{{ auth()->user()->role }}">
                    {{ auth()->user()->role_label }}
                </span>
                <small class="text-muted">{{ now()->isoFormat('dddd, D MMMM Y') }}</small>
            </div>
        </div>

        <div class="content-wrapper">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2" role="alert">
                    <i class="bi bi-check-circle-fill"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center gap-2" role="alert">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @yield('content')
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
