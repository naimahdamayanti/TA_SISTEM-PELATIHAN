<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') &mdash; Expertindo</title>

    <link rel="shortcut icon" href="{{ asset('template/assets/img/logo/logo-expertindo.png') }}">
    {{-- Bootstrap 5 --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    {{-- Bootstrap Icons --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    {{-- Google Font: Outfit --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --brand:       #e84e3a;
            --brand-dark:  #c0392b;
            --brand-light: #fff0ee;
            --sidebar-w:   240px;
            --topbar-h:    60px;
        }

        *, *::before, *::after { box-sizing: border-box; }

        body {
            font-family: 'Outfit', sans-serif;
            background: #f5f6fa;
            min-height: 100vh;
        }

        /* ─── SIDEBAR ─── */
        #sidebar {
            position: fixed;
            top: 0; left: 0;
            width: var(--sidebar-w);
            height: 100vh;
            background: #fff;
            border-right: 1px solid #eee;
            display: flex;
            flex-direction: column;
            z-index: 1040;
            transition: transform .25s ease;
        }

        .sidebar-brand {
            height: var(--topbar-h);
            padding: 0 20px;
            display: flex;
            align-items: center;
            border-bottom: 1px solid #eee;
            flex-shrink: 0;
        }
        .sidebar-brand img { height: 34px; }

        .sidebar-nav {
            flex: 1;
            overflow-y: auto;
            padding: 16px 12px;
        }
        .sidebar-nav::-webkit-scrollbar { width: 4px; }
        .sidebar-nav::-webkit-scrollbar-thumb { background: #e0e0e0; border-radius: 4px; }

        .nav-section-label {
            font-size: 10px;
            font-weight: 700;
            letter-spacing: .08em;
            text-transform: uppercase;
            color: #aaa;
            padding: 10px 10px 4px;
        }

        .sidebar-link {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 9px 12px;
            border-radius: 8px;
            color: #555;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            transition: background .15s, color .15s;
            margin-bottom: 2px;
        }
        .sidebar-link i { font-size: 17px; flex-shrink: 0; }
        .sidebar-link:hover { background: var(--brand-light); color: var(--brand); }
        .sidebar-link.active { background: var(--brand-light); color: var(--brand); font-weight: 600; }

        /* ─── SIDEBAR FOOTER (user info) ─── */
        .sidebar-footer {
            padding: 14px 16px;
            border-top: 1px solid #eee;
            display: flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
            transition: background .15s;
            border-radius: 0 0 0 0;
        }
        .sidebar-footer:hover { background: var(--brand-light); }
        .sidebar-footer .sf-avatar {
            width: 36px; height: 36px;
            background: linear-gradient(135deg, var(--brand), var(--brand-dark));
            border-radius: 50%;
            color: #fff;
            font-weight: 700;
            font-size: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .sidebar-footer .sf-name { font-weight: 600; font-size: 13px; line-height: 1.2; }
        .sidebar-footer .sf-role { font-size: 11px; color: #aaa; }

        /* ─── TOPBAR ─── */
        #topbar {
            position: fixed;
            top: 0;
            left: var(--sidebar-w);
            right: 0;
            height: var(--topbar-h);
            background: #fff;
            border-bottom: 1px solid #eee;
            display: flex;
            align-items: center;
            padding: 0 24px;
            gap: 12px;
            z-index: 1030;
        }

        .topbar-toggle {
            border: none;
            background: none;
            font-size: 20px;
            color: #555;
            cursor: pointer;
            padding: 4px 6px;
            border-radius: 6px;
            transition: background .15s;
        }
        .topbar-toggle:hover { background: #f0f0f0; }

        .topbar-title {
            font-size: 16px;
            font-weight: 600;
            color: #333;
        }

        .topbar-right { margin-left: auto; display: flex; align-items: center; gap: 10px; }

        /* topbar profile button */
        .topbar-profile {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 6px 12px;
            border: 1.5px solid #e5e5e5;
            border-radius: 999px;
            background: #fff;
            cursor: pointer;
            transition: border-color .15s, background .15s;
            text-decoration: none;
            color: #333;
        }
        .topbar-profile:hover { border-color: var(--brand); background: var(--brand-light); }
        .topbar-profile .tp-avatar {
            width: 28px; height: 28px;
            background: linear-gradient(135deg, var(--brand), var(--brand-dark));
            border-radius: 50%;
            color: #fff;
            font-weight: 700;
            font-size: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .topbar-profile .tp-name { font-size: 13px; font-weight: 600; }
        .topbar-profile .tp-caret { font-size: 12px; color: #aaa; }

        /* logout */
        .btn-keluar {
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 6px 16px;
            border: 1.5px solid var(--brand);
            border-radius: 999px;
            color: var(--brand);
            font-size: 13px;
            font-weight: 600;
            background: #fff;
            cursor: pointer;
            text-decoration: none;
            transition: background .15s, color .15s;
        }
        .btn-keluar:hover { background: var(--brand); color: #fff; }

        /* ─── MAIN CONTENT ─── */
        #main-content {
            margin-left: var(--sidebar-w);
            margin-top: var(--topbar-h);
            padding: 28px 28px 40px;
            min-height: calc(100vh - var(--topbar-h));
        }

        /* ─── RESPONSIVE ─── */
        @media (max-width: 991.98px) {
            #sidebar { transform: translateX(calc(-1 * var(--sidebar-w))); }
            #sidebar.open { transform: translateX(0); box-shadow: 4px 0 20px rgba(0,0,0,.12); }
            #topbar { left: 0; }
            #main-content { margin-left: 0; padding: 20px 16px 32px; }
        }

        /* ─── BTN PRIMARY brand color ─── */
        .btn-primary {
            background-color: var(--brand);
            border-color: var(--brand);
        }
        .btn-primary:hover, .btn-primary:focus {
            background-color: var(--brand-dark);
            border-color: var(--brand-dark);
        }
        .btn-outline-primary { color: var(--brand); border-color: var(--brand); }
        .btn-outline-primary:hover { background-color: var(--brand); border-color: var(--brand); color: #fff; }
        a { color: var(--brand); }
        .text-primary { color: var(--brand) !important; }
        .bg-primary { background-color: var(--brand) !important; }
        .bg-primary-subtle { background-color: var(--brand-light) !important; }

        /* pagination active */
        .page-item.active .page-link {
            background-color: var(--brand);
            border-color: var(--brand);
        }
        .page-link { color: var(--brand); }
        .page-link:hover { color: var(--brand-dark); }
    </style>

    @stack('styles')
</head>
<body>

{{-- ══════════════════════════════════
     SIDEBAR
══════════════════════════════════ --}}
<aside id="sidebar">
    <div class="sidebar-brand">
        <img src="{{ asset('template/assets/img/logo/logo-expertindo.png') }}"
                class="img-fluid" alt="logo">
    </div>

    {{-- Nav --}}
    <nav class="sidebar-nav">
        <div class="nav-section-label">Utama</div>
        <a href="{{ route('dashboard') }}"
           class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="bi bi-house-door"></i> Dashboard
        </a>

        <div class="nav-section-label">Manajemen</div>
        <a href="{{ route('admin.pelatihan.index') }}"
           class="sidebar-link {{ request()->routeIs('admin.pelatihan.*') ? 'active' : '' }}">
            <i class="bi bi-journal-bookmark"></i> Kelola Pelatihan
        </a>
        <a href="{{ route('admin.pendaftaran.index') }}"
           class="sidebar-link {{ request()->routeIs('admin.pendaftaran.*') ? 'active' : '' }}">
            <i class="bi bi-people"></i> Kelola Pendaftaran
        </a>
        <a href="{{ route('admin.instruktur.index') }}"
           class="sidebar-link {{ request()->routeIs('admin.instruktur.*') ? 'active' : '' }}">
            <i class="bi bi-person-video3"></i> Kelola Instruktur
        </a>
        <a href="{{ route('admin.sertifikat.index') }}"
           class="sidebar-link {{ request()->routeIs('admin.sertifikat.*') ? 'active' : '' }}">
            <i class="bi bi-award"></i> Sertifikat
        </a>
        <a href="{{ route('admin.laporan.index') }}"
           class="sidebar-link {{ request()->routeIs('admin.laporan.*') ? 'active' : '' }}">
            <i class="bi bi-bar-chart-line"></i> Laporan
        </a>

        <div class="nav-section-label">Sistem</div>
        <a href="{{ route('admin.akun.index') }}"
           class="sidebar-link {{ request()->routeIs('admin.akun.*') ? 'active' : '' }}">
            <i class="bi bi-person-gear"></i> Kelola Akun
        </a>
    </nav>

    {{-- Footer: klik buka modal profil --}}
    <div class="sidebar-footer" data-open-profil title="Edit Profil">
        <div class="sf-avatar">{{ strtoupper(substr(Auth::user()->username, 0, 1)) }}</div>
        <div class="flex-fill min-w-0">
            <div class="sf-name text-truncate">{{ Auth::user()->nama }}</div>
            <div class="sf-role">{{ ucfirst(Auth::user()->role) }}</div>
        </div>
        <i class="bi bi-three-dots-vertical text-muted" style="font-size:14px"></i>
    </div>
</aside>

{{-- ══════════════════════════════════
     TOPBAR
══════════════════════════════════ --}}
<header id="topbar">
    <button class="topbar-toggle" id="sidebarToggle" aria-label="Toggle Sidebar">
        <i class="bi bi-list"></i>
    </button>
    <span class="topbar-title d-none d-sm-inline">@yield('title', 'Dashboard')</span>

    <div class="topbar-right">
        {{-- Profil button: klik buka modal --}}
        <a href="{{ route('admin.akun.index') }}" class="topbar-profile" data-open-profil title="Edit Profil">
            <div class="tp-avatar">{{ strtoupper(substr(Auth::user()->username, 0, 1)) }}</div>
            <span class="tp-name d-none d-md-inline">{{ Auth::user()->nama }}</span>
            <i class="bi bi-chevron-down tp-caret"></i>
        </a>

        {{-- Logout --}}
        <form action="{{ route('logout') }}" method="POST" class="d-inline">
            @csrf
            <button type="submit" class="btn-keluar">
                <i class="bi bi-box-arrow-right"></i>
                <span class="d-none d-sm-inline">Keluar</span>
            </button>
        </form>
    </div>
</header>

{{-- Overlay (mobile) --}}
<div id="sidebarOverlay"
     style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.35);z-index:1039"
     onclick="closeSidebar()"></div>

{{-- ══════════════════════════════════
     MAIN CONTENT
══════════════════════════════════ --}}
<main id="main-content">
    @yield('content')
</main>

{{-- Bootstrap JS --}}
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // Sidebar toggle (mobile)
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');

    document.getElementById('sidebarToggle').addEventListener('click', () => {
        const isOpen = sidebar.classList.toggle('open');
        overlay.style.display = isOpen ? 'block' : 'none';
    });

    function closeSidebar() {
        sidebar.classList.remove('open');
        overlay.style.display = 'none';
    }
</script>

@stack('scripts')
</body>
</html>