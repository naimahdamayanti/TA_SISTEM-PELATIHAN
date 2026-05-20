@extends('layouts.peserta')

@section('title', 'Status Kehadiran')
@section('page-title', 'Status Kehadiran')

@push('styles')
<style>
    /* ─── PAGE HEADING ─── */
    .page-heading {
        margin-bottom: 24px;
    }
    .page-heading h5 {
        font-size: 17px;
        font-weight: 700;
        color: #1a1a2e;
        margin: 0;
    }

    /* ─── KEHADIRAN CARD ─── */
    .kehadiran-card {
        background: #fff;
        border: 1px solid #eee;
        border-radius: 14px;
        padding: 22px 24px;
        margin-bottom: 16px;
        transition: box-shadow .2s;
    }
    .kehadiran-card:hover { box-shadow: 0 6px 24px rgba(0,0,0,.07); }
    .kehadiran-card:last-child { margin-bottom: 0; }

    /* Card header */
    .kc-header { margin-bottom: 16px; }
    .kc-nama {
        font-size: 15px;
        font-weight: 700;
        color: #1a1a2e;
        margin-bottom: 3px;
    }
    .kc-meta {
        font-size: 12px;
        color: #aaa;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .kc-meta .sep { opacity: .4; }

    /* ─── CHIP SESI ─── */
    .sesi-chips {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        margin-bottom: 16px;
    }

    .sesi-chip {
        width: 36px;
        height: 36px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 13px;
        font-weight: 700;
        cursor: default;
        position: relative;
        transition: transform .15s;
    }
    .sesi-chip:hover { transform: translateY(-2px); }

    /* Tooltip tanggal */
    .sesi-chip::after {
        content: attr(data-tooltip);
        position: absolute;
        bottom: calc(100% + 6px);
        left: 50%;
        transform: translateX(-50%);
        background: #1a1a2e;
        color: #fff;
        font-size: 10px;
        font-weight: 500;
        padding: 3px 7px;
        border-radius: 5px;
        white-space: nowrap;
        opacity: 0;
        pointer-events: none;
        transition: opacity .15s;
        z-index: 10;
    }
    .sesi-chip:hover::after { opacity: 1; }

    /* Warna per status */
    .chip-hadir {
        background: #dcfce7;
        color: #15803d;
        border: 1.5px solid #bbf7d0;
    }
    .chip-izin {
        background: #fef9c3;
        color: #854d0e;
        border: 1.5px solid #fef08a;
    }
    .chip-sakit {
        background: #ffe4e6;
        color: #9f1239;
        border: 1.5px solid #fecdd3;
    }
    .chip-alpha {
        background: #fee2e2;
        color: #991b1b;
        border: 1.5px solid #fecaca;
    }
    .chip-belum {
        background: #f3f4f6;
        color: #9ca3af;
        border: 1.5px solid #e5e7eb;
    }

    /* ─── PROGRESS BAR ─── */
    .progress-row {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 14px;
    }
    .progress-track {
        flex: 1;
        height: 8px;
        background: #f0f0f0;
        border-radius: 999px;
        overflow: hidden;
    }
    .progress-fill {
        height: 100%;
        border-radius: 999px;
        background: #22c55e;
        transition: width .5s ease;
    }
    .progress-fill.medium { background: #f59e0b; }
    .progress-fill.low    { background: #ef4444; }
    .progress-pct {
        font-size: 13px;
        font-weight: 700;
        min-width: 38px;
        text-align: right;
    }
    .pct-high   { color: #15803d; }
    .pct-medium { color: #b45309; }
    .pct-low    { color: #991b1b; }

    /* ─── LEGENDA ─── */
    .legenda {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }
    .legenda-item {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 4px 12px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 600;
    }
    .leg-hadir  { background: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; }
    .leg-izin   { background: #fef9c3; color: #854d0e; border: 1px solid #fef08a; }
    .leg-sakit  { background: #ffe4e6; color: #9f1239; border: 1px solid #fecdd3; }
    .leg-alpha  { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
    .leg-belum  { background: #f3f4f6; color: #9ca3af; border: 1px solid #e5e7eb; }

    /* ─── EMPTY STATE ─── */
    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #bbb;
    }
    .empty-state i {
        font-size: 48px;
        display: block;
        margin-bottom: 12px;
        opacity: .35;
    }
    .empty-state p { font-size: 14px; margin: 0; }
    .empty-state a {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        margin-top: 16px;
        padding: 9px 20px;
        background: var(--brand);
        color: #fff;
        border-radius: 8px;
        font-size: 13.5px;
        font-weight: 600;
        text-decoration: none;
        transition: background .15s;
    }
    .empty-state a:hover { background: var(--brand-dark); }
</style>
@endpush

@section('content')

{{-- ── Heading ── --}}
<div class="page-heading">
    <h5>Status Kehadiran</h5>
</div>

{{-- ── Konten ── --}}
@if($dataKehadiran->isEmpty())

    <div class="empty-state">
        <i class="bi bi-calendar-x"></i>
        <p>Anda belum mengikuti pelatihan apapun.</p>
        <a href="{{ route('peserta.pelatihan.index') }}">
            <i class="bi bi-collection"></i> Lihat Katalog Pelatihan
        </a>
    </div>

@else

    @foreach($dataKehadiran as $item)
        @php
            $pelatihan = $item['pelatihan'];
            $sesiList  = $item['sesi'];
            $hadir     = $item['hadir'];
            $total     = $item['total'];
            $persen    = $item['persen'];

            // Warna progress sesuai persentase
            $progressClass = $persen >= 75 ? '' : ($persen >= 50 ? 'medium' : 'low');
            $pctClass      = $persen >= 75 ? 'pct-high' : ($persen >= 50 ? 'pct-medium' : 'pct-low');

            // Hitung per-status untuk legenda
            $jmlIzin  = $sesiList->where('status', 'izin')->count();
            $jmlSakit = $sesiList->where('status', 'sakit')->count();
            $jmlAlpha = $sesiList->where('status', 'alpha')->count();
            $jmlBelum = $sesiList->where('status', 'belum dicatat')->count();

            // Singkatan chip per status
            $chipLabel = [
                'hadir'        => 'H',
                'izin'         => 'I',
                'sakit'        => 'S',
                'alpha'        => 'A',
                'belum dicatat'=> '–',
            ];
            $chipClass = [
                'hadir'        => 'chip-hadir',
                'izin'         => 'chip-izin',
                'sakit'        => 'chip-sakit',
                'alpha'        => 'chip-alpha',
                'belum dicatat'=> 'chip-belum',
            ];
        @endphp

        <div class="kehadiran-card">

            {{-- Header --}}
            <div class="kc-header">
                <div class="kc-nama">{{ $pelatihan->nama_pelatihan }}</div>
                <div class="kc-meta">
                    <span>{{ $pelatihan->kode_pelatihan }}</span>
                    @if($pelatihan->tgl_mulai && $pelatihan->tgl_selesai)
                        <span class="sep">·</span>
                        <span>
                            {{ \Carbon\Carbon::parse($pelatihan->tgl_mulai)->translatedFormat('j M Y') }}
                            –
                            {{ \Carbon\Carbon::parse($pelatihan->tgl_selesai)->translatedFormat('j M Y') }}
                        </span>
                    @endif
                </div>
            </div>

            {{-- Chips sesi --}}
            @if($sesiList->isNotEmpty())
                <div class="sesi-chips">
                    @foreach($sesiList as $s)
                        @php
                            $st     = $s['status'];
                            $label  = $chipLabel[$st]  ?? '?';
                            $cls    = $chipClass[$st]   ?? 'chip-belum';
                            $tgl    = \Carbon\Carbon::parse($s['sesi']->tanggal)->translatedFormat('D, j M Y');
                            $judul  = $s['sesi']->judul_sesi ? ' · ' . $s['sesi']->judul_sesi : '';
                            $tooltip = $tgl . $judul . ' (' . ucfirst($st) . ')';
                        @endphp
                        <div class="sesi-chip {{ $cls }}"
                             data-tooltip="{{ $tooltip }}">
                            {{ $label }}
                        </div>
                    @endforeach
                </div>
            @else
                <p style="font-size:13px;color:#bbb;margin-bottom:16px">
                    Belum ada sesi untuk pelatihan ini.
                </p>
            @endif

            {{-- Progress bar --}}
            <div class="progress-row">
                <div class="progress-track">
                    <div class="progress-fill {{ $progressClass }}"
                         style="width: {{ $persen }}%"></div>
                </div>
                <span class="progress-pct {{ $pctClass }}">{{ $persen }}%</span>
            </div>

            {{-- Legenda --}}
            <div class="legenda">
                @if($hadir > 0)
                    <span class="legenda-item leg-hadir">H = Hadir ({{ $hadir }})</span>
                @endif
                @if($jmlIzin > 0)
                    <span class="legenda-item leg-izin">I = Izin ({{ $jmlIzin }})</span>
                @endif
                @if($jmlSakit > 0)
                    <span class="legenda-item leg-sakit">S = Sakit ({{ $jmlSakit }})</span>
                @endif
                @if($jmlAlpha > 0)
                    <span class="legenda-item leg-alpha">A = Alpha ({{ $jmlAlpha }})</span>
                @endif
                @if($jmlBelum > 0)
                    <span class="legenda-item leg-belum">– = Belum Dicatat ({{ $jmlBelum }})</span>
                @endif
                @if($total === 0)
                    <span class="legenda-item leg-belum">Belum ada sesi</span>
                @endif
            </div>

        </div>{{-- /kehadiran-card --}}
    @endforeach

@endif

@endsection