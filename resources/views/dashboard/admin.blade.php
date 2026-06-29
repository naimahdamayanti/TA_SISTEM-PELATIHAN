@extends('layouts.admin')

@section('title', 'Dashboard Admin')

@section('content')

{{-- ══════════════════════════════════════
     PAGE HEADER
══════════════════════════════════════ --}}
<div class="d-flex align-items-start justify-content-between mb-4">
    <div>
        <h4 class="fw-bold mb-1">Dashboard</h4>
    </div>
</div>

{{-- ══════════════════════════════════════
     STAT CARDS
══════════════════════════════════════ --}}
<div class="row g-3 mb-4">

    {{-- Total Peserta --}}
    <div class="col-6 col-xl-auto flex-xl-fill">
        <div class="stat-card card border-0 shadow-sm rounded-3 h-100">
            <div class="card-body d-flex align-items-center gap-3 py-3 px-4">
                <div class="stat-icon rounded-3 d-flex align-items-center justify-content-center"
                     style="width:52px;height:52px;background:linear-gradient(135deg,#ff6b3d,#ff9a7b);flex-shrink:0">
                    <i class="bi bi-people-fill text-white fs-5"></i>
                </div>
                <div>
                    <div class="stat-number fw-bold lh-1 mb-1" style="font-size:1.8rem">
                        {{ $totalPeserta }}
                    </div>
                    <div class="fw-semibold small">Total Peserta</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Total Instruktur --}}
    <div class="col-6 col-xl-auto flex-xl-fill">
        <div class="stat-card card border-0 shadow-sm rounded-3 h-100">
            <div class="card-body d-flex align-items-center gap-3 py-3 px-4">
                <div class="stat-icon rounded-3 d-flex align-items-center justify-content-center"
                     style="width:52px;height:52px;background:linear-gradient(135deg,#9b59b6,#d2b4de);flex-shrink:0">
                    <i class="bi bi-person-badge-fill text-white fs-5"></i>
                </div>
                <div>
                    <div class="stat-number fw-bold lh-1 mb-1" style="font-size:1.8rem">
                        {{ $totalInstruktur }}
                    </div>
                    <div class="fw-semibold small">Total Instruktur</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Pelatihan Berlangsung --}}
    <div class="col-6 col-xl-auto flex-xl-fill">
        <div class="stat-card card border-0 shadow-sm rounded-3 h-100">
            <div class="card-body d-flex align-items-center gap-3 py-3 px-4">
                <div class="stat-icon rounded-3 d-flex align-items-center justify-content-center"
                     style="width:52px;height:52px;background:linear-gradient(135deg,#2ecc71,#82e0aa);flex-shrink:0">
                    <i class="bi bi-calendar-check-fill text-white fs-5"></i>
                </div>
                <div>
                    <div class="stat-number fw-bold lh-1 mb-1" style="font-size:1.8rem">
                        {{ $pelatihanAktif }}
                    </div>
                    <div class="fw-semibold small">Pelatihan Berlangsung</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Pelatihan Selesai --}}
    <div class="col-6 col-xl-auto flex-xl-fill">
        <div class="stat-card card border-0 shadow-sm rounded-3 h-100">
            <div class="card-body d-flex align-items-center gap-3 py-3 px-4">
                <div class="stat-icon rounded-3 d-flex align-items-center justify-content-center"
                     style="width:52px;height:52px;background:linear-gradient(135deg,#3498db,#85c1e9);flex-shrink:0">
                    <i class="bi bi-patch-check-fill text-white fs-5"></i>
                </div>
                <div>
                    <div class="stat-number fw-bold lh-1 mb-1" style="font-size:1.8rem">
                        {{ $pelatihanSelesai }}
                    </div>
                    <div class="fw-semibold small">Pelatihan Selesai</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Pendaftar Baru --}}
    <div class="col-6 col-xl-auto flex-xl-fill">
        <div class="stat-card card border-0 shadow-sm rounded-3 h-100">
            <div class="card-body d-flex align-items-center gap-3 py-3 px-4">
                <div class="stat-icon rounded-3 d-flex align-items-center justify-content-center"
                    style="width:52px;height:52px;background:linear-gradient(135deg,#f1c40f,#f9e49d);flex-shrink:0">
                    <i class="bi bi-person-plus-fill text-white fs-5"></i>
                </div>
                <div>
                    <div class="stat-number fw-bold lh-1 mb-1" style="font-size:1.8rem">
                        {{ $pendaftarBaru }}
                    </div>
                    <div class="fw-semibold small">Pendaftar Baru</div>
                </div>
            </div>
        </div>
    </div>

</div>

{{-- ══════════════════════════════════════
     GRAFIK  +  TOP PELATIHAN
══════════════════════════════════════ --}}
<div class="row g-4 mb-4">

    {{-- Grafik Bulanan --}}
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-3 h-100">
            <div class="card-header bg-white border-bottom py-3 px-4 d-flex align-items-center justify-content-between">
                <h6 class="fw-bold mb-0">Grafik Pelatihan Bulanan {{ $tahun }}</h6>
                <form method="GET" action="{{ route('dashboard') }}">
                    <select name="tahun" class="form-select form-select-sm" style="width:90px"
                            onchange="this.form.submit()">
                        @for($y = \Carbon\Carbon::now()->year; $y >= \Carbon\Carbon::now()->year - 4; $y--)
                            <option value="{{ $y }}" @selected($y == $tahun)>{{ $y }}</option>
                        @endfor
                    </select>
                </form>
            </div>
            <div class="card-body px-4 py-3" style="height:280px">
                <canvas id="grafikBulanan"></canvas>
            </div>
        </div>
    </div>

    {{-- Top Pelatihan --}}
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm rounded-3 h-100">
            <div class="card-header bg-white border-bottom py-3 px-4">
                <h6 class="fw-bold mb-0">Top Pelatihan Diminati</h6>
            </div>
            <div class="card-body px-4 py-3">
                @forelse($topPelatihan as $idx => $item)
                <div class="d-flex align-items-center gap-3 {{ !$loop->last ? 'mb-3' : '' }}">
                    <div class="top-rank rounded-circle fw-bold d-flex align-items-center justify-content-center text-white"
                         style="width:30px;height:30px;font-size:13px;flex-shrink:0;
                                background:{{ $idx === 0 ? '#e74c3c' : ($idx === 1 ? '#e67e22' : ($idx === 2 ? '#3498db' : '#95a5a6')) }}">
                        {{ $idx + 1 }}
                    </div>
                    <div class="flex-fill min-w-0">
                        <div class="fw-semibold small text-truncate">{{ $item->nama_pelatihan }}</div>
                        <div class="text-muted" style="font-size:11px">
                            {{ $item->kategori->nama_kategori ?? '-' }} &middot; {{ $item->kode_pelatihan }}
                        </div>
                    </div>
                    <div class="fw-bold small text-primary text-nowrap">
                        {{ $item->pendaftaran_count }} <span class="text-muted fw-normal">peserta</span>
                    </div>
                </div>
                @empty
                <p class="text-muted small text-center mt-3">Belum ada data pelatihan.</p>
                @endforelse
            </div>
        </div>
    </div>

</div>


{{-- ══════════════════════════════════════
     MODAL PROFIL
══════════════════════════════════════ --}}
<div class="modal fade" id="modalProfil" tabindex="-1" aria-labelledby="modalProfilLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-4">
            <div class="modal-header border-0 pb-0 px-4 pt-4">
                <h5 class="modal-title fw-bold" id="modalProfilLabel">Detail Profil</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body px-4 pb-0">

                {{-- Avatar --}}
                <div class="text-center mb-4">
                    <div class="avatar-circle mx-auto mb-2 d-flex align-items-center justify-content-center
                                rounded-circle text-white fw-bold position-relative"
                         style="width:80px;height:80px;font-size:2rem;
                                background:linear-gradient(135deg,#ff6b3d,#e84e3a)">
                        {{ strtoupper(substr(Auth::user()->nama, 0, 1)) }}
                        <span class="position-absolute bottom-0 end-0 bg-white rounded-circle border border-2
                                     border-white d-flex align-items-center justify-content-center"
                              style="width:24px;height:24px">
                            <i class="bi bi-camera-fill text-secondary" style="font-size:11px"></i>
                        </span>
                    </div>
                    <div class="fw-bold">{{ Auth::user()->nama }}</div>
                    <div class="text-muted small">{{ ucfirst(Auth::user()->role) }}</div>
                </div>

                @if(session('profil_success'))
                <div class="alert alert-success rounded-3 py-2 small">
                    <i class="bi bi-check-circle-fill me-1"></i> {{ session('profil_success') }}
                </div>
                @endif

                <form action="{{ route('profil.update') }}" method="POST" id="formProfil">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">
                            Nama Lengkap
                        </label>
                        <input type="text" name="name" class="form-control rounded-3
                               @error('name') is-invalid @enderror"
                               value="{{ old('nama', Auth::user()->nama) }}">
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">
                            Username 
                        </label>
                        <input type="text" name="username" class="form-control rounded-3
                               @error('username') is-invalid @enderror"
                               value="{{ old('username', Auth::user()->username) }}">
                        @error('username') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">
                            Email 
                        </label>
                        <input type="email" name="email" class="form-control rounded-3
                               @error('email') is-invalid @enderror"
                               value="{{ old('email', Auth::user()->email) }}">
                        @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">
                            No. HP
                        </label>
                        <input type="text" name="no_hp" class="form-control rounded-3
                               @error('no_hp') is-invalid @enderror"
                               value="{{ old('no_hp', Auth::user()->no_hp ?? '') }}">
                        @error('no_hp') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <hr class="my-3">

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">
                            Password Baru <span class="text-muted fw-normal">(opsional)</span>
                        </label>
                        <input type="password" name="password" class="form-control rounded-3
                               @error('password') is-invalid @enderror"
                               placeholder="Biarkan kosong jika tidak diubah">
                        @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
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

@push('styles')
<style>
    .stat-card {
        transition: transform .18s ease, box-shadow .18s ease;
    }
    .stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 24px rgba(0,0,0,.10) !important;
    }
</style>
@endpush

@push('scripts')
{{-- Chart.js --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
(function () {
    const bulanLabel = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
    const grafikData = @json(array_values($grafikData));  {{-- array_values agar indeks 0-11 --}}

    const ctx = document.getElementById('grafikBulanan').getContext('2d');

    // Highlight bulan ini
    const bulanIni = new Date().getMonth(); // 0-based
    const bgColors = grafikData.map((_, i) =>
        i === bulanIni ? '#e84e3a' : 'rgba(255, 107, 61, 0.55)'
    );
    const hoverColors = grafikData.map((_, i) =>
        i === bulanIni ? '#c0392b' : 'rgba(255, 107, 61, 0.8)'
    );

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: bulanLabel,
            datasets: [{
                label: 'Jumlah Pelatihan',
                data: grafikData,
                backgroundColor: bgColors,
                hoverBackgroundColor: hoverColors,
                borderRadius: 6,
                borderSkipped: false,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: ctx => ` ${ctx.parsed.y} pelatihan`
                    }
                }
            },
            scales: {
                x: {
                    grid: { display: false },
                    border: { display: false },
                    ticks: { font: { size: 12 } }
                },
                y: {
                    grid: { color: '#f0f0f0' },
                    border: { display: false, dash: [4, 4] },
                    ticks: {
                        stepSize: 1,
                        precision: 0,
                        font: { size: 12 }
                    },
                    beginAtZero: true
                }
            }
        }
    });

    // ── Modal Profil: buka dari topbar & sidebar ──
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
})();
</script>
@endpush

@endsection