@extends('layouts.peserta')

@section('title', 'Dashboard Peserta')
@section('page-title', 'Dashboard Peserta')

@section('content')

<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h5 class="fw-bold mb-1">Dashboard Peserta</h5>
        <p class="text-muted small mb-0">
            Selamat datang, <strong>{{ Auth::user()->nama_lengkap ?? Auth::user()->nama ?? 'Peserta' }}</strong>
        </p>
    </div>
</div>

<div class="row g-3 mb-4">

    <div class="col-6 col-lg-3">
        <div class="stat-card">
            <div class="stat-icon" style="background:#fff4f0">
                <i class="bi bi-clipboard-check" style="color:#e84e3a"></i>
            </div>
            <div>
                <div class="stat-label">Pelatihan Terdaftar</div>
                <div class="stat-value">{{ $totalDaftar }}</div>
                <div class="stat-sub">Total pendaftaran</div>
            </div>
        </div>
    </div>

    <div class="col-6 col-lg-3">
        <div class="stat-card">
            <div class="stat-icon" style="background:#f0fdf4">
                <i class="bi bi-play-circle" style="color:#16a34a"></i>
            </div>
            <div>
                <div class="stat-label">Sedang Berlangsung</div>
                <div class="stat-value">{{ $pelatihanAktif->count() }}</div>
                <div class="stat-sub">Status tersedia</div>
            </div>
        </div>
    </div>

    <div class="col-6 col-lg-3">
        <div class="stat-card">
            <div class="stat-icon" style="background:#eff6ff">
                <i class="bi bi-check-circle" style="color:#2563eb"></i>
            </div>
            <div>
                <div class="stat-label">Pelatihan Selesai</div>
                @php
                    $jmlSelesai = $pendaftaran->filter(fn($p) =>
                        $p->status === 'diterima' && $p->pelatihan?->status === 'selesai'
                    )->count();
                @endphp
                <div class="stat-value">{{ $jmlSelesai }}</div>
                <div class="stat-sub">Status selesai</div>
            </div>
        </div>
    </div>

    <div class="col-6 col-lg-3">
        <div class="stat-card">
            <div class="stat-icon" style="background:#fefce8">
                <i class="bi bi-award" style="color:#ca8a04"></i>
            </div>
            <div>
                <div class="stat-label">Sertifikat Terkumpul</div>
                <div class="stat-value">{{ $totalSertifikat }}</div>
                <div class="stat-sub">Sudah diterima</div>
            </div>
        </div>
    </div>

</div>

<div class="row g-4">

    <div class="col-lg-8">
        <div class="panel">
            <div class="panel-header">
                <h6>Pelatihan Saya</h6>
                <a href="{{ route('peserta.pelatihan.index') }}" style="font-size:12px;text-decoration:none">
                    Lihat semua →
                </a>
            </div>
            <div class="table-responsive">
                <table class="table-custom">
                    <thead>
                        <tr>
                            <th>Nama Pelatihan</th>
                            <th>Kode</th>
                            <th>Status Pendaftaran</th>
                            <th>Status Pelatihan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pendaftaran as $item)
                        <tr>
                            <td class="fw-semibold" style="color:#222">
                                {{ $item->pelatihan?->nama_pelatihan ?? '-' }}
                            </td>
                            <td>
                                <span class="kode-badge">
                                    {{ $item->pelatihan?->kode_pelatihan ?? '-' }}
                                </span>
                            </td>
                            <td>
                                <span class="badge-{{ $item->status }}">
                                    {{ ucfirst($item->status) }}
                                </span>
                            </td>
                            <td>
                                @if($item->pelatihan)
                                    <span class="badge-{{ $item->pelatihan->status }}">
                                        {{ ucfirst($item->pelatihan->status) }}
                                    </span>
                                @else
                                    <span class="text-muted" style="font-size:12px">–</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4">
                                <div class="empty-state">
                                    <i class="bi bi-inbox"></i>
                                    Belum ada pendaftaran pelatihan.
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-4 d-flex flex-column gap-3">

        <div class="panel">
            <div class="panel-header">
                <h6>Sertifikat Terbaru</h6>
                <a href="{{ route('peserta.sertifikat.index') }}" style="font-size:12px;text-decoration:none">
                    Lihat semua →
                </a>
            </div>

            @php
                $sertifikatTerbaru = \App\Models\SertifikatModel::whereHas('pendaftaran', fn($q) =>
                    $q->where('peserta_id', Auth::user()->id_user)
                )->with('pendaftaran.pelatihan')->latest()->first();

                $templateUrl = $sertifikatTerbaru?->pendaftaran?->pelatihan?->template_sertifikat
                    ? \Illuminate\Support\Facades\Storage::url(
                        $sertifikatTerbaru->pendaftaran->pelatihan->template_sertifikat
                    )
                    : null;
            @endphp

            @if($sertifikatTerbaru)
                <div style="padding:16px 16px 8px">

                    @if($templateUrl)
                        <div style="position:relative; width:100%; aspect-ratio:297/210;
                                    border-radius:10px; overflow:hidden; margin-bottom:12px;">
                            <img src="{{ $templateUrl }}"
                                style="width:100%;height:100%;object-fit:fill;display:block;">
                            <div style="position:absolute;inset:0;background:rgba(0,0,0,0.18);"></div>
                            <div style="position:absolute;inset:0;display:flex;flex-direction:column;
                                        align-items:center;justify-content:center;gap:3px;padding:10px;">
                                <div style="font-size:11px;font-weight:700;color:#fff;
                                            text-shadow:0 1px 4px rgba(0,0,0,0.6);text-align:center;">
                                    {{ Auth::user()->nama_lengkap ?? Auth::user()->name }}
                                </div>
                                <div style="font-size:9px;color:rgba(255,255,255,0.85);
                                            text-shadow:0 1px 3px rgba(0,0,0,0.5);text-align:center;">
                                    {{ $sertifikatTerbaru->pendaftaran?->pelatihan?->nama_pelatihan ?? '-' }}
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="sertifikat-preview mb-3">
                            <div style="font-size:28px;margin-bottom:6px">🏆</div>
                            <div style="font-size:10px;font-weight:700;letter-spacing:.1em;
                                        color:#aaa;text-transform:uppercase;margin-bottom:6px">
                                Sertifikat Kelulusan
                            </div>
                            <div class="sertifikat-nama">
                                {{ Auth::user()->nama_lengkap ?? Auth::user()->name }}
                            </div>
                            <div style="font-size:12px;color:#555;margin:4px 0">
                                {{ $sertifikatTerbaru->pendaftaran?->pelatihan?->nama_pelatihan ?? '-' }}
                            </div>
                        </div>
                    @endif

                    <div style="text-align:center;margin-bottom:10px;">
                        <div style="font-size:11px;color:#555;font-weight:600;">
                            {{ $sertifikatTerbaru->nomor_sertifikat ?? '-' }}
                        </div>
                        <div style="font-size:10px;color:#bbb;font-family:monospace;margin-top:2px;">
                            {{ $sertifikatTerbaru->kode_sertifikat }}
                        </div>
                    </div>

                    <a href="{{ route('peserta.sertifikat.download', $sertifikatTerbaru->id_sertifikat ?? $sertifikatTerbaru->id) }}"
                    class="btn btn-primary w-100 rounded-3 fw-semibold d-flex align-items-center justify-content-center gap-2"
                    target="_blank">
                        <i class="bi bi-download"></i> Unduh PDF
                    </a>
                </div>
            @else
                <div class="empty-state">
                    <i class="bi bi-award"></i>
                    Belum ada sertifikat tersedia.
                </div>
            @endif
        </div>

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
                            {{ $sesi->pelatihan?->nama_pelatihan ?? '–' }}
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