<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>@yield('title')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- CSS --}}
    <link rel="stylesheet" href="{{ asset('template/assets/css/style.css') }}">
</head>

<body>

{{-- ================= NAVBAR ================= --}}
<nav class="navbar bg-white border-bottom fixed-top px-3">
    <h5 class="mb-0">@yield('judul')</h5>

    <div class="ms-auto">
        <span>{{ session('nama') }}</span>
    </div>
</nav>


{{-- ================= SIDEBAR ================= --}}
<aside class="sidebar">
    <div class="logo-area p-3">
        <b>My App</b>
    </div>

    @php
    $user = session('user');
    $menus = [];

    if ($user && $user->role === 'admin') {
        $menus = [
            ['title' => 'Dashboard', 'route' => 'dashboard', 'icon' => 'lni lni-home'],
            ['title' => 'Pelatihan', 'route' => 'pelatihan.index', 'icon' => 'lni lni-graduation'],
            ['title' => 'Peserta', 'route' => 'peserta.index', 'icon' => 'lni lni-users'],
            ['title' => 'Instruktur', 'route' => 'instruktur.index', 'icon' => 'lni lni-user'],
            ['title' => 'Sertifikat', 'route' => 'sertifikat.index', 'icon' => 'lni lni-certificate'],
        ];
    } elseif ($user && $user->role === 'instruktur') {
        $menus = [
            ['title' => 'Dashboard', 'route' => 'dashboard', 'icon' => 'lni lni-home'],
            ['title' => 'Pelatihan Saya', 'route' => 'pelatihan.saya', 'icon' => 'lni lni-book'],
            ['title' => 'Peserta', 'route' => 'peserta.saya', 'icon' => 'lni lni-users'],
        ];
    } elseif ($user && $user->role === 'user') {
        $menus = [
            ['title' => 'Dashboard', 'route' => 'dashboard', 'icon' => 'lni lni-home'],
            ['title' => 'Pelatihan Saya', 'route' => 'pelatihan.saya', 'icon' => 'lni lni-book'],
            ['title' => 'Sertifikat', 'route' => 'sertifikat.saya', 'icon' => 'lni lni-certificate'],
        ];
    }
    @endphp

    <ul class="nav flex-column">

        @foreach($menus as $menu)
            <li class="nav-item mb-2">
                <a href="{{ route($menu['route']) }}"
                   class="nav-link d-flex align-items-center 
                   {{ request()->routeIs($menu['route'].'*') ? 'active bg-primary text-white' : '' }}">
                   
                    <i class="{{ $menu['icon'] }} me-2"></i>
                    <span>{{ $menu['title'] }}</span>
                </a>
            </li>
        @endforeach

        {{-- LOGOUT --}}
        <li class="nav-item mt-3">
            <a href="{{ route('logout') }}" class="nav-link text-danger">
                <i class="lni lni-exit me-2"></i> Logout
            </a>
        </li>

    </ul>
</aside>


{{-- ================= CONTENT ================= --}}
<main class="content" style="margin-top:70px; margin-left:250px;">
    <div class="container-fluid p-4">
        @yield('isi')
    </div>
</main>

<script src="{{ asset('template/assets/js/main.js') }}"></script>

{{-- tempat script tambahan (chart, dll) --}}
@yield('script')

</body>
</html>