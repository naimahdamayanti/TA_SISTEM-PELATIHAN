<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') &mdash; Expertindo</title>

    <link rel="shortcut icon" href="{{ asset('template/assets/img/logo/logo-expertindo.png') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
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

        /* badge notifikasi di sidebar */
        .sidebar-badge {
            margin-left: auto;
            background: var(--brand);
            color: #fff;
            font-size: 10px;
            font-weight: 700;
            padding: 1px 7px;
            border-radius: 999px;
            line-height: 1.6;
            flex-shrink: 0;
        }

        /* ─── SIDEBAR FOOTER ─── */
        .sidebar-footer {
            padding: 14px 16px;
            border-top: 1px solid #eee;
            display: flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
            transition: background .15s;
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

        .topbar-title { font-size: 16px; font-weight: 600; color: #333; }
        .topbar-right { margin-left: auto; display: flex; align-items: center; gap: 10px; }

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

        #sidebar { transition: transform .25s ease, width .25s ease; }

        @media (min-width: 992px) {
            #sidebar.collapsed { width: 64px; }
            #sidebar.collapsed .nav-section-label,
            #sidebar.collapsed .link-text,
            #sidebar.collapsed .sidebar-badge,
            #sidebar.collapsed .sf-name,
            #sidebar.collapsed .sf-role { display: none; }
            #sidebar.collapsed .sidebar-brand { justify-content: center; padding: 0; }
            #sidebar.collapsed .sidebar-brand img { height: 28px; max-width: 40px; object-fit: contain; }
            #sidebar.collapsed .sidebar-link { justify-content: center; padding: 10px 0; }
            #sidebar.collapsed .sidebar-link i { font-size: 20px; margin: 0; }
            #sidebar.collapsed .sidebar-footer { justify-content: center; padding: 14px 0; }
            #sidebar.collapsed ~ #topbar       { left: 64px; }
            #sidebar.collapsed ~ #main-content { margin-left: 64px; }
            #topbar, #main-content { transition: left .25s ease, margin-left .25s ease; }
        }

        /* ─── BRAND COLOR OVERRIDES ─── */
        .btn-primary { background-color: var(--brand); border-color: var(--brand); }
        .btn-primary:hover, .btn-primary:focus { background-color: var(--brand-dark); border-color: var(--brand-dark); }
        .btn-outline-primary { color: var(--brand); border-color: var(--brand); }
        .btn-outline-primary:hover { background-color: var(--brand); border-color: var(--brand); color: #fff; }
        a { color: var(--brand); }
        .text-primary { color: var(--brand) !important; }
        .bg-primary { background-color: var(--brand) !important; }
        .bg-primary-subtle { background-color: var(--brand-light) !important; }
        .page-item.active .page-link { background-color: var(--brand); border-color: var(--brand); }
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

    <nav class="sidebar-nav">

        {{-- ── Utama ── --}}
        <div class="nav-section-label">Utama</div>
        <a href="{{ route('dashboard') }}"
           class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"
           title="Dashboard">
            <i class="bi bi-house-door"></i>
            <span class="link-text">Dashboard</span>
        </a>

        {{-- ── Manajemen ── --}}
        <div class="nav-section-label">Manajemen</div>

        <a href="{{ route('admin.pelatihan.index') }}"
           class="sidebar-link {{ request()->routeIs('admin.pelatihan.*') ? 'active' : '' }}"
           title="Kelola Pelatihan">
            <i class="bi bi-journal-bookmark"></i>
            <span class="link-text">Kelola Pelatihan</span>
        </a>

        <a href="{{ route('admin.pendaftaran.index') }}"
           class="sidebar-link {{ request()->routeIs('admin.pendaftaran.*') ? 'active' : '' }}"
           title="Kelola Pendaftaran">
            <i class="bi bi-people"></i>
            <span class="link-text">Kelola Pendaftaran</span>
        </a>

        <a href="{{ route('admin.instruktur.index') }}"
           class="sidebar-link {{ request()->routeIs('admin.instruktur.*') ? 'active' : '' }}"
           title="Kelola Instruktur">
            <i class="bi bi-person-video3"></i>
            <span class="link-text">Kelola Instruktur</span>
        </a>

        {{-- ↓ BARU: Kode Penerimaan Instruktur --}}
        <a href="{{ route('admin.kode-penerimaan.index') }}"
           class="sidebar-link {{ request()->routeIs('admin.kode-penerimaan.*') ? 'active' : '' }}"
           title="Kode Penerimaan Instruktur">
            <i class="bi bi-key"></i>
            <span class="link-text">Kode Penerimaan</span>
            @php
                $menungguVerif = \App\Models\UserModel::where('role','instruktur')
                                    ->where('status_verifikasi','menunggu')
                                    ->count();
            @endphp
            @if($menungguVerif > 0)
                <span class="sidebar-badge">{{ $menungguVerif }}</span>
            @endif
        </a>

        <a href="{{ route('admin.sertifikat.index') }}"
           class="sidebar-link {{ request()->routeIs('admin.sertifikat.*') ? 'active' : '' }}"
           title="Sertifikat">
            <i class="bi bi-award"></i>
            <span class="link-text">Sertifikat</span>
        </a>

        <a href="{{ route('admin.laporan.index') }}"
           class="sidebar-link {{ request()->routeIs('admin.laporan.*') ? 'active' : '' }}"
           title="Laporan">
            <i class="bi bi-bar-chart-line"></i>
            <span class="link-text">Laporan</span>
        </a>

        {{-- ── Sistem ── --}}
        <div class="nav-section-label">Sistem</div>

        <a href="{{ route('admin.akun.index') }}"
           class="sidebar-link {{ request()->routeIs('admin.akun.*') ? 'active' : '' }}"
           title="Kelola Akun">
            <i class="bi bi-person-gear"></i>
            <span class="link-text">Kelola Akun</span>
        </a>

    </nav>

    {{-- Footer --}}
    <div class="sidebar-footer" data-open-profil title="Edit Profil">
        <div class="sf-avatar">
            {{ strtoupper(substr(Auth::user()->nama ?? 'U', 0, 1)) }}
        </div>
        <div class="flex-fill" style="min-width:0">
            <div class="sf-name text-truncate">{{ Auth::user()->nama }}</div>
            <div class="sf-role">{{ ucfirst(Auth::user()->role) }}</div>
        </div>
        <i class="bi bi-three-dots-vertical text-muted" style="font-size:14px;flex-shrink:0"></i>
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
        <button type="button" class="topbar-profile" data-open-profil title="Edit Profil">
            <div class="tp-avatar">
                {{ strtoupper(substr(Auth::user()->nama ?? 'U', 0, 1)) }}
            </div>
            <span class="tp-name d-none d-md-inline">{{ Auth::user()->nama }}</span>
            <i class="bi bi-chevron-down tp-caret"></i>
        </button>

        <form action="{{ route('logout') }}" method="POST" class="d-inline">
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

{{-- ══════════════════════════════════
     MAIN CONTENT
══════════════════════════════════ --}}
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
                            {{ strtoupper(substr(Auth::user()->nama ?? 'U', 0, 1)) }}
                        </div>
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');

    document.getElementById('sidebarToggle').addEventListener('click', () => {
        if (window.innerWidth >= 992) {
            sidebar.classList.toggle('collapsed');
        } else {
            const isOpen = sidebar.classList.toggle('open');
            overlay.style.display = isOpen ? 'block' : 'none';
        }
    });

    function closeSidebar() {
        sidebar.classList.remove('open');
        overlay.style.display = 'none';
    }

    const modalProfilEl = document.getElementById('modalProfil');

    // getOrCreateInstance — mencegah duplikasi instance pada elemen yang sama
    function getModalProfil() {
        return bootstrap.Modal.getOrCreateInstance(modalProfilEl);
    }

    document.querySelectorAll('[data-open-profil]').forEach(el => {
        el.addEventListener('click', e => {
            e.preventDefault();
            getModalProfil().show();
        });
    });

    @if($errors->any())
        getModalProfil().show();
    @endif

    @if(session('profil_success'))
        getModalProfil().show();
    @endif

    // Safety net: paksa bersihkan backdrop yang tertinggal setelah modal benar-benar tertutup
    modalProfilEl.addEventListener('hidden.bs.modal', function () {
        document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
        document.body.classList.remove('modal-open');
        document.body.style.overflow = '';
    });
</script>

@stack('scripts')
</body>
</html>