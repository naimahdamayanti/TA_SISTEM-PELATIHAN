<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Instruktur') – Expertindo</title>

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

        /* ─── SCROLLBAR ─── */
        ::-webkit-scrollbar { width: 4px; height: 4px; }
        ::-webkit-scrollbar-track { background: #f1f1f1; }
        ::-webkit-scrollbar-thumb { background: #ddd; border-radius: 4px; }

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
        .sidebar-link:hover  { background: var(--brand-light); color: var(--brand); }
        .sidebar-link.active { background: var(--brand-light); color: var(--brand); font-weight: 600; }

        /* ─── SIDEBAR FOOTER ─── */
        .sidebar-footer {
            padding: 14px 16px;
            border-top: 1px solid #eee;
            display: flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
            transition: background .15s;
            flex-shrink: 0;
        }
        .sidebar-footer:hover { background: var(--brand-light); }
        .sf-avatar {
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
        .sf-name { font-weight: 600; font-size: 13px; line-height: 1.2; }
        .sf-role { font-size: 11px; color: #aaa; }

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
            padding: 4px 8px;
            border-radius: 6px;
            line-height: 1;
            transition: background .15s, color .15s;
        }
        .topbar-toggle:hover { background: #f0f0f0; color: var(--brand); }

        .topbar-title { font-size: 16px; font-weight: 600; color: #333; }
        .topbar-right  { margin-left: auto; display: flex; align-items: center; gap: 10px; }

        /* topbar profile button */
        .topbar-profile {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 6px 14px;
            border: 1.5px solid #e5e5e5;
            border-radius: 999px;
            background: #fff;
            cursor: pointer;
            transition: border-color .15s, background .15s, color .15s;
            color: #333;
        }
        .topbar-profile:hover { border-color: var(--brand); background: var(--brand-light); color: var(--brand); }
        .tp-avatar {
            width: 28px; height: 28px;
            background: linear-gradient(135deg, var(--brand), var(--brand-dark));
            border-radius: 50%;
            color: #fff;
            font-weight: 700;
            font-size: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .tp-name  { font-size: 13px; font-weight: 600; }
        .tp-caret { font-size: 12px; color: #aaa; }

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

        /* ─── BRAND OVERRIDES ─── */
        .btn-primary { background-color: var(--brand); border-color: var(--brand); }
        .btn-primary:hover, .btn-primary:focus { background-color: var(--brand-dark); border-color: var(--brand-dark); }
        .btn-outline-primary { color: var(--brand); border-color: var(--brand); }
        .btn-outline-primary:hover { background-color: var(--brand); border-color: var(--brand); color: #fff; }
        a { color: var(--brand); }
        .text-primary { color: var(--brand) !important; }
        .bg-primary   { background-color: var(--brand) !important; }
        .bg-primary-subtle { background-color: var(--brand-light) !important; }
        .page-item.active .page-link { background-color: var(--brand); border-color: var(--brand); }
        .page-link { color: var(--brand); }
        .page-link:hover { color: var(--brand-dark); }

        /* ─── STAT CARDS ─── */
        .stat-card {
            background: #fff;
            border: 1px solid #eee;
            border-radius: 12px;
            padding: 20px;
            display: flex;
            align-items: flex-start;
            gap: 16px;
            height: 100%;
            transition: transform .2s, box-shadow .2s;
        }
        .stat-card:hover { transform: translateY(-3px); box-shadow: 0 8px 24px rgba(0,0,0,.08); }
        .stat-icon {
            width: 44px; height: 44px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            font-size: 20px;
        }
        .stat-label { font-size: 11px; color: #888; margin-bottom: 2px; line-height: 1.3; }
        .stat-value { font-size: 28px; font-weight: 700; color: #222; line-height: 1; }
        .stat-sub   { font-size: 11px; color: #bbb; margin-top: 2px; }

        /* ─── BADGE STATUS ─── */
        .badge-tersedia { background: #dcfce7; color: #15803d; padding: 3px 10px; border-radius: 999px; font-size: 11px; font-weight: 600; white-space: nowrap; }
        .badge-selesai  { background: #f3f4f6; color: #374151; padding: 3px 10px; border-radius: 999px; font-size: 11px; font-weight: 600; white-space: nowrap; }
        .badge-menunggu { background: #fef9c3; color: #854d0e; padding: 3px 10px; border-radius: 999px; font-size: 11px; font-weight: 600; white-space: nowrap; }

        /* ─── QUICK ACTION LINKS ─── */
        .quick-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 14px;
            border: 1px solid #e5e5e5;
            border-radius: 10px;
            color: #444;
            text-decoration: none;
            font-size: 13px;
            font-weight: 500;
            transition: border-color .15s, background .15s, color .15s;
        }
        .quick-link + .quick-link { margin-top: 8px; }
        .quick-link i { font-size: 16px; flex-shrink: 0; color: #bbb; transition: color .15s; }
        .quick-link:hover { border-color: var(--brand); color: var(--brand); background: var(--brand-light); }
        .quick-link:hover i { color: var(--brand); }

        /* ─── MINI CALENDAR ─── */
        .mini-cal {
            width: 40px;
            flex-shrink: 0;
            background: var(--brand-light);
            border-radius: 8px;
            text-align: center;
            padding: 6px 0;
        }
        .mc-month { font-size: 9px;  font-weight: 700; color: var(--brand); text-transform: uppercase; line-height: 1; }
        .mc-day   { font-size: 16px; font-weight: 700; color: var(--brand); line-height: 1.2; }

        /* ─── LOGBOOK CARD ─── */
        .logbook-card { background: var(--brand); border-radius: 12px; padding: 20px; color: #fff; }
        .lc-icon  { width: 40px; height: 40px; background: rgba(255,255,255,.2); border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 18px; flex-shrink: 0; }
        .lc-label { font-size: 11px; color: rgba(255,255,255,.7); }
        .lc-value { font-size: 26px; font-weight: 700; }
        .lc-desc  { font-size: 11px; color: rgba(255,255,255,.6); margin-top: 10px; }

        /* ─── PANEL ─── */
        .panel { background: #fff; border: 1px solid #eee; border-radius: 12px; overflow: hidden; }
        .panel-header { padding: 14px 20px; border-bottom: 1px solid #f0f0f0; display: flex; align-items: center; justify-content: space-between; }
        .panel-header h6 { margin: 0; font-size: 14px; font-weight: 600; color: #333; }

        /* ─── TABLE ─── */
        .table-custom { width: 100%; font-size: 13px; border-collapse: collapse; margin: 0; }
        .table-custom thead th {
            padding: 10px 16px;
            text-align: left;
            font-size: 11px;
            font-weight: 700;
            color: #888;
            text-transform: uppercase;
            letter-spacing: .05em;
            background: #f9fafb;
            border-bottom: 1px solid #f0f0f0;
        }
        .table-custom tbody tr { border-bottom: 1px solid #f8f8f8; transition: background .12s; }
        .table-custom tbody tr:hover { background: #fafafa; }
        .table-custom tbody td { padding: 12px 16px; color: #444; vertical-align: middle; }
        .kode-badge { font-family: monospace; font-size: 12px; background: #f3f4f6; color: #555; padding: 2px 8px; border-radius: 4px; }
        .empty-state { padding: 40px 16px; text-align: center; color: #bbb; font-size: 13px; }
        .empty-state i { font-size: 32px; display: block; margin-bottom: 8px; }
    </style>

    @stack('styles')
</head>

<body>

{{-- ══════════════════════════════
     SIDEBAR
══════════════════════════════ --}}
<aside id="sidebar">
    <div class="sidebar-brand">
        <img src="{{ asset('template/assets/img/logo/logo-expertindo.png') }}" alt="Expertindo">
    </div>

    <nav class="sidebar-nav">
        <div class="nav-section-label">Utama</div>
        <a href="{{ route('dashboard') }}"
           class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="bi bi-house-door"></i> Dashboard
        </a>

        <div class="nav-section-label">Pelatihan</div>
        <a href="{{ route('instruktur.pelatihan.index') }}"
           class="sidebar-link {{ request()->routeIs('instruktur.pelatihan.*') ? 'active' : '' }}">
            <i class="bi bi-journal-bookmark"></i> Pelatihan Saya
        </a>
        <a href="{{ route('instruktur.logbook.index') }}"
           class="sidebar-link {{ request()->routeIs('instruktur.logbook.*') ? 'active' : '' }}">
            <i class="bi bi-clipboard-check"></i> Logbook Kehadiran
        </a>
        <a href="{{ route('instruktur.kelayakan.index') }}"
           class="sidebar-link {{ request()->routeIs('instruktur.kelayakan.*') ? 'active' : '' }}">
            <i class="bi bi-shield-check"></i> Status Kelayakan
        </a>
        <a href="{{ route('instruktur.sertifikat.index') }}"
           class="sidebar-link {{ request()->routeIs('instruktur.sertifikat.*') ? 'active' : '' }}">
            <i class="bi bi-award"></i> Riwayat Sertifikat
        </a>
    </nav>

    {{-- Footer sidebar → buka modal profil --}}
    <div class="sidebar-footer" data-open-profil title="Edit Profil">
        <div class="sf-avatar">{{
            
        strtoupper(substr(Auth::user()->username, 0, 1)) }}</div>
        <div class="flex-fill" style="min-width:0">
            <div class="sf-name text-truncate">{{ Auth::user()->nama }}</div>
            <div class="sf-role">{{ ucfirst(Auth::user()->role) }}</div>
        </div>
        <i class="bi bi-three-dots-vertical text-muted" style="font-size:14px;flex-shrink:0"></i>
    </div>
</aside>

{{-- ══════════════════════════════
     TOPBAR
══════════════════════════════ --}}
<header id="topbar">
    <button class="topbar-toggle" id="sidebarToggle" aria-label="Toggle Sidebar">
        <i class="bi bi-list"></i>
    </button>
    <span class="topbar-title d-none d-sm-inline">@yield('page-title', 'Dashboard')</span>

    <div class="topbar-right">
        {{-- Profil → buka modal profil --}}
        <button type="button" class="topbar-profile" data-open-profil title="Edit Profil">
            <div class="tp-avatar">{{ strtoupper(substr(Auth::user()->username, 0, 1)) }}</div>
            <span class="tp-name d-none d-md-inline">{{ Auth::user()->nama }}</span>
            <i class="bi bi-chevron-down tp-caret"></i>
        </button>

        {{-- Logout --}}
        <form action="{{ route('logout') }}" method="POST" class="d-inline m-0">
            @csrf
            <button type="submit" class="btn-keluar">
                <i class="bi bi-box-arrow-right"></i>
                <span class="d-none d-sm-inline">Keluar</span>
            </button>
        </form>
    </div>
</header>

{{-- Overlay mobile --}}
<div id="sidebarOverlay"
     style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.35);z-index:1039"
     onclick="closeSidebar()"></div>

{{-- ══════════════════════════════
     MAIN CONTENT
══════════════════════════════ --}}
<main id="main-content">
    @yield('content')
</main>

{{-- ══════════════════════════════════════════════════════
     MODAL DETAIL PROFIL
══════════════════════════════════════════════════════ --}}
<div class="modal fade" id="modalProfil" tabindex="-1"
     aria-labelledby="modalProfilLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow rounded-4">

            <div class="modal-header border-0 pb-0 px-4 pt-4">
                <h5 class="modal-title fw-bold" id="modalProfilLabel">Detail Profil</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body px-4 pb-0">

                {{-- Avatar --}}
                <div class="text-center mb-4">
                    <div class="position-relative d-inline-block">
                        <div class="d-flex align-items-center justify-content-center rounded-circle text-white fw-bold mx-auto"
                             style="width:80px;height:80px;font-size:2rem;
                                    background:linear-gradient(135deg,#ff6b3d,#e84e3a)">
                            {{ strtoupper(substr(Auth::user()->nama, 0, 1)) }}
                        </div>
                        <span class="position-absolute bottom-0 end-0 bg-white rounded-circle border border-2
                                     border-white d-flex align-items-center justify-content-center"
                              style="width:24px;height:24px;cursor:pointer">
                            <i class="bi bi-camera-fill text-secondary" style="font-size:11px"></i>
                        </span>
                    </div>
                    <div class="fw-bold mt-2">{{ Auth::user()->nama }}</div>
                    <div class="text-muted small">{{ ucfirst(Auth::user()->role) }}</div>
                </div>

                @if(session('profil_success'))
                <div class="alert alert-success rounded-3 py-2 small">
                    <i class="bi bi-check-circle-fill me-1"></i> {{ session('profil_success') }}
                </div>
                @endif

                <form action="{{ route('profil.update') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Nama Lengkap</label>
                        <input type="text" name="nama"
                               class="form-control rounded-3 @error('nama') is-invalid @enderror"
                               value="{{ old('nama', Auth::user()->nama) }}">
                        @error('nama')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Username</label>
                        <input type="text" name="username"
                               class="form-control rounded-3 @error('username') is-invalid @enderror"
                               value="{{ old('username', Auth::user()->username) }}">
                        @error('username')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Email</label>
                        <input type="email" name="email"
                               class="form-control rounded-3 @error('email') is-invalid @enderror"
                               value="{{ old('email', Auth::user()->email) }}">
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">No. HP</label>
                        <input type="text" name="no_hp"
                               class="form-control rounded-3 @error('no_hp') is-invalid @enderror"
                               value="{{ old('no_hp', Auth::user()->no_hp ?? '') }}">
                        @error('no_hp')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <hr>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">
                            Password Baru <span class="text-muted fw-normal">(opsional)</span>
                        </label>
                        <input type="password" name="password"
                               class="form-control rounded-3 @error('password') is-invalid @enderror"
                               placeholder="Biarkan kosong jika tidak diubah">
                        @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-4">
                        <label class="form-label small fw-semibold">Konfirmasi Password</label>
                        <input type="password" name="password_confirmation"
                               class="form-control rounded-3"
                               placeholder="Ulangi password baru">
                    </div>

                    <div class="d-flex gap-2 pb-4">
                        <button type="button" class="btn btn-outline-secondary flex-fill rounded-3"
                                data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary flex-fill rounded-3 fw-semibold">
                            <i class="bi bi-check-lg me-1"></i>Simpan Perubahan
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>

{{-- Bootstrap JS --}}
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // ── Sidebar toggle (mobile) ──────────────────────────
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

    // ── Modal Profil ─────────────────────────────────────
    const modalProfil = new bootstrap.Modal(document.getElementById('modalProfil'));

    document.querySelectorAll('[data-open-profil]').forEach(el => {
        el.addEventListener('click', e => {
            e.preventDefault();
            modalProfil.show();
        });
    });

    // Auto-buka jika ada error validasi profil
    @if($errors->any())
        modalProfil.show();
    @endif

    // Auto-buka jika ada pesan sukses profil
    @if(session('profil_success'))
        modalProfil.show();
    @endif
</script>

@stack('scripts')

</body>
</html>