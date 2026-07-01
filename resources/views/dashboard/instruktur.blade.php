@extends('layouts.instruktur')

@section('title', 'Dashboard Instruktur')
@section('page-title', 'Dashboard Instruktur')

@section('content')

@if(Auth::user()->status_verifikasi === 'menunggu')
    <div class="alert d-flex align-items-start gap-3 rounded-3 mb-4 border-0"
         style="background:#fffbeb;border-left:4px solid #f59e0b!important;border-left-width:4px;">
        <i class="bi bi-hourglass-split fs-5 mt-1" style="color:#f59e0b"></i>
        <div>
            <div class="fw-semibold mb-1" style="color:#78350f">Akun Sedang Menunggu Verifikasi</div>
            <div class="small" style="color:#92400e">
                Dokumen penerimaan Anda sedang diperiksa oleh Admin.
                Anda belum dapat ditugaskan ke sesi pelatihan sampai akun diverifikasi.
                Hubungi Admin jika membutuhkan informasi lebih lanjut.
            </div>
        </div>
    </div>

@elseif(Auth::user()->status_verifikasi === 'ditolak')
    <div class="alert d-flex align-items-start gap-3 rounded-3 mb-4 border-0"
         style="background:#fef2f2;border-left:4px solid #dc2626!important;border-left-width:4px;">
        <i class="bi bi-x-circle fs-5 mt-1 text-danger"></i>
        <div>
            <div class="fw-semibold mb-1 text-danger">Verifikasi Dokumen Ditolak</div>
            <div class="small text-danger-emphasis">
                @if(Auth::user()->catatan_verifikasi)
                    <strong>Alasan:</strong> {{ Auth::user()->catatan_verifikasi }}<br>
                @endif
                Hubungi Admin PT. Expertindo Training untuk informasi lebih lanjut
                atau ajukan kembali dokumen yang sesuai.
            </div>
        </div>
    </div>
@endif

<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h5 class="fw-bold mb-1">Dashboard Instruktur</h5>
        <p class="text-muted small mb-0">
            Selamat datang, <strong>{{ Auth::user()->nama ?? 'Instruktur' }}</strong>
        </p>
    </div>
</div>

<div class="row g-3 mb-4">

    <div class="col-6 col-lg-3">
        <div class="stat-card">
            <div class="stat-icon" style="background:#fff4f0">
                <i class="bi bi-calendar-event" style="color:#e84e3a"></i>
            </div>
            <div>
                <div class="fw-semibold small">Pelatihan Berlangsung</div>
                <div class="stat-number fw-bold lh-1 mb-1" style="font-size:1.8rem">{{ $pelatihanAktif }}</div>
            </div>
        </div>
    </div>

    <div class="col-6 col-lg-3">
        <div class="stat-card">
            <div class="stat-icon" style="background:#f0fdf4">
                <i class="bi bi-check-circle" style="color:#16a34a"></i>
            </div>
            <div>
                <div class="fw-semibold small">Pelatihan Selesai</div>
                <div class="stat-number fw-bold lh-1 mb-1" style="font-size:1.8rem">{{ $pelatihanSelesai }}</div>
            </div>
        </div>
    </div>

    <div class="col-6 col-lg-3">
        <div class="stat-card">
            <div class="stat-icon" style="background:#eff6ff">
                <i class="bi bi-people" style="color:#2563eb"></i>
            </div>
            <div>
                <div class="fw-semibold small">Total Peserta Diampu</div>
                <div class="stat-number fw-bold lh-1 mb-1" style="font-size:1.8rem">{{ $pelatihan->sum('pendaftaran_count') }}</div>
            </div>
        </div>
    </div>

    <div class="col-6 col-lg-3">
        <div class="stat-card">
            <div class="stat-icon" style="background:#fefce8">
                <i class="bi bi-award" style="color:#ca8a04"></i>
            </div>
            <div>
                <div class="fw-semibold small">Sertifikat Diterbitkan</div>
                <div class="stat-number fw-bold lh-1 mb-1" style="font-size:1.8rem">{{ $totalPelatihan }}</div>
            </div>
        </div>
    </div>

</div>

<div class="row g-4">

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

    <div class="col-lg-4 d-flex flex-column gap-3">

        <div class="panel">
            <div class="panel-header"><h6>Aksi Cepat</h6></div>
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

        @if($sesiMendatang->isNotEmpty())
        <div class="panel">
            <div class="panel-header"><h6>Sesi Mendatang</h6></div>
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