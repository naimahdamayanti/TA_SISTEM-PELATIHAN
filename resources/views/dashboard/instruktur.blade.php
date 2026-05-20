@extends('layouts.instruktur')

@section('title', 'Dashboard Instruktur')
@section('page-title', 'Dashboard Instruktur')

@section('content')

{{-- ── Greeting ── --}}
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h5 class="fw-bold mb-1">Dashboard Instruktur</h5>
        <p class="text-muted small mb-0">
            Selamat datang, <strong>{{ Auth::user()->nama ?? 'Instruktur' }}</strong>
        </p>
    </div>
</div>

{{-- ── STAT CARDS ── --}}
<div class="row g-3 mb-4">

    {{-- Pelatihan Berlangsung --}}
    <div class="col-6 col-lg-3">
        <div class="stat-card">
            <div class="stat-icon" style="background:#fff4f0">
                <i class="bi bi-calendar-event" style="color:#e84e3a"></i>
            </div>
            <div>
                <div class="stat-label">Pelatihan Berlangsung</div>
                <div class="stat-value">{{ $pelatihanAktif }}</div>
                <div class="stat-sub">Sedang diampu</div>
            </div>
        </div>
    </div>

    {{-- Pelatihan Selesai --}}
    <div class="col-6 col-lg-3">
        <div class="stat-card">
            <div class="stat-icon" style="background:#f0fdf4">
                <i class="bi bi-check-circle" style="color:#16a34a"></i>
            </div>
            <div>
                <div class="stat-label">Pelatihan Selesai</div>
                <div class="stat-value">{{ $pelatihanSelesai }}</div>
                <div class="stat-sub">Total diampu</div>
            </div>
        </div>
    </div>

    {{-- Total Peserta --}}
    <div class="col-6 col-lg-3">
        <div class="stat-card">
            <div class="stat-icon" style="background:#eff6ff">
                <i class="bi bi-people" style="color:#2563eb"></i>
            </div>
            <div>
                <div class="stat-label">Total Peserta Diampu</div>
                <div class="stat-value">{{ $pelatihan->sum('pendaftaran_count') }}</div>
                <div class="stat-sub">Status diterima</div>
            </div>
        </div>
    </div>

    {{-- Sertifikat Diterbitkan --}}
    <div class="col-6 col-lg-3">
        <div class="stat-card">
            <div class="stat-icon" style="background:#fefce8">
                <i class="bi bi-award" style="color:#ca8a04"></i>
            </div>
            <div>
                <div class="stat-label">Sertifikat Diterbitkan</div>
                <div class="stat-value">{{ $totalPelatihan }}</div>
                <div class="stat-sub">Pelatihan saya</div>
            </div>
        </div>
    </div>

</div>

{{-- ── GRID: Tabel + Kolom Kanan ── --}}
<div class="row g-4">

    {{-- Tabel Pelatihan Saya --}}
    <div class="col-lg-8">
        <div class="panel">
            <div class="panel-header">
                <h6>Pelatihan Saya</h6>
                <a href="{{ route('instruktur.pelatihan.index') }}"
                   style="font-size:12px;text-decoration:none">Lihat semua →</a>
            </div>
            <div class="table-responsive">
                <table class="table-custom">
                    <thead>
                        <tr>
                            <th class="text-muted">Nama</th>
                            <th class="text-muted">Kode</th>
                            <th class="text-muted">Peserta</th>
                            <th class="text-muted">Periode</th>
                            <th class="text-muted">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pelatihan as $p)
                        <tr>
                            <td class="fw-semibold" style="color:#222">{{ $p->nama_pelatihan }}</td>
                            <td><span class="kode-badge">{{ $p->kode_pelatihan }}</span></td>
                            <td>{{ $p->pendaftaran_count }}/{{ $p->kapasitas ?? '–' }}</td>
                            <td style="white-space:nowrap;font-size:12px;color:#888">
                                {{ \Carbon\Carbon::parse($p->tgl_mulai)->translatedFormat('j M Y') }}
                                – {{ \Carbon\Carbon::parse($p->tgl_selesai)->translatedFormat('j M Y') }}
                            </td>
                            <td><span class="badge-{{ $p->status }}">{{ ucfirst($p->status) }}</span></td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5">
                                <div class="empty-state">
                                    <i class="bi bi-inbox"></i>
                                    Belum ada pelatihan yang diampu.
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Kolom Kanan --}}
    <div class="col-lg-4 d-flex flex-column gap-3">

        {{-- Aksi Cepat --}}
        <div class="panel">
            <div class="panel-header">
                <h6>Aksi Cepat</h6>
            </div>
            <div style="padding:16px 16px 8px">
                <a href="{{ route('instruktur.logbook.index') }}" class="quick-link">
                    <i class="bi bi-clipboard-plus"></i>
                    Input Logbook Kehadiran
                </a>

                <a href="{{ route('instruktur.kelayakan.index') }}" class="quick-link">
                    <i class="bi bi-shield-check"></i>
                    <span class="flex-fill">Nilai Kelayakan Peserta</span>
                    @if($menungguKelayakan > 0)
                        <span class="badge rounded-pill"
                              style="background:var(--brand);font-size:11px">
                            {{ $menungguKelayakan }}
                        </span>
                    @endif
                </a>

                <a href="{{ route('instruktur.sertifikat.index') }}" class="quick-link">
                    <i class="bi bi-award"></i>
                    Riwayat Sertifikat
                </a>
            </div>
        </div>

        {{-- Sesi Mendatang --}}
        @if($sesiMendatang->isNotEmpty())
        <div class="panel">
            <div class="panel-header">
                <h6>Sesi Mendatang</h6>
            </div>
            <div style="padding:16px">
                @foreach($sesiMendatang as $sesi)
                <div class="d-flex align-items-start gap-3 {{ !$loop->last ? 'mb-3' : '' }}">
                    <div class="mini-cal">
                        <div class="mc-month">
                            {{ \Carbon\Carbon::parse($sesi->tanggal)->translatedFormat('M') }}
                        </div>
                        <div class="mc-day">
                            {{ \Carbon\Carbon::parse($sesi->tanggal)->format('d') }}
                        </div>
                    </div>
                    <div style="min-width:0;flex:1">
                        <div class="fw-semibold text-truncate" style="font-size:12px;color:#333">
                            {{ $sesi->pelatihan->nama_pelatihan ?? '–' }}
                        </div>
                        <div style="font-size:11px;color:#aaa;margin-top:2px">
                            {{ \Carbon\Carbon::parse($sesi->waktu_mulai)->format('H:i') }}
                            –
                            {{ \Carbon\Carbon::parse($sesi->waktu_selesai)->format('H:i') }}
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</div>

@endsection