@extends('layouts.instruktur')

@section('title', 'Pelatihan Saya')
@section('page-title', 'Pelatihan Saya')

@push('styles')
<style>
    /* ─── PELATIHAN CARD ─── */
    .pelatihan-card {
        background: #fff;
        border: 1px solid #eee;
        border-radius: 14px;
        padding: 22px 22px 18px;
        height: 100%;
        display: flex;
        flex-direction: column;
        transition: box-shadow .2s, transform .2s;
    }
    .pelatihan-card:hover {
        box-shadow: 0 6px 28px rgba(0,0,0,.09);
        transform: translateY(-2px);
    }

    /* ─── Card header ─── */
    .card-top {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 10px;
        margin-bottom: 10px;
    }
    .kode-pill {
        font-family: monospace;
        font-size: 12px;
        background: #f3f4f6;
        color: #555;
        padding: 4px 10px;
        border-radius: 6px;
        font-weight: 600;
        white-space: nowrap;
    }

    /* ─── Badge status ─── */
    .badge-tersedia { background:#dcfce7; color:#15803d; padding:4px 12px; border-radius:999px; font-size:11px; font-weight:700; white-space:nowrap; }
    .badge-selesai  { background:#f3f4f6; color:#374151; padding:4px 12px; border-radius:999px; font-size:11px; font-weight:700; white-space:nowrap; }
    .badge-menunggu { background:#fef9c3; color:#854d0e; padding:4px 12px; border-radius:999px; font-size:11px; font-weight:700; white-space:nowrap; }
    .badge-penuh    { background:#fee2e2; color:#b91c1c; padding:4px 12px; border-radius:999px; font-size:11px; font-weight:700; white-space:nowrap; }

    /* ─── Card body text ─── */
    .card-title { font-size: 17px; font-weight: 700; color: #1a1a1a; margin: 0 0 4px; line-height: 1.3; }
    .card-meta  { font-size: 12px; color: #999; margin-bottom: 18px; }
    .card-meta span + span::before { content: '·'; margin: 0 5px; }

    /* ─── Stats row ─── */
    .card-stats {
        display: flex;
        gap: 24px;
        margin-bottom: 20px;
    }
    .cs-item { display: flex; flex-direction: column; gap: 2px; }
    .cs-label { font-size: 11px; color: #aaa; }
    .cs-value { font-size: 20px; font-weight: 700; color: #222; line-height: 1; }

    /* ─── Divider ─── */
    .card-divider { border: none; border-top: 1px solid #f0f0f0; margin: 0 0 16px; }

    /* ─── Buka Logbook button ─── */
    .btn-logbook {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        width: 100%;
        padding: 10px;
        border: 1.5px solid var(--brand);
        border-radius: 10px;
        color: var(--brand);
        font-size: 13px;
        font-weight: 600;
        background: #fff;
        text-decoration: none;
        transition: background .15s, color .15s;
        margin-top: auto;
    }
    .btn-logbook:hover { background: var(--brand); color: #fff; }
    .btn-logbook i { font-size: 15px; }

    /* ─── Filter bar ─── */
    .filter-bar {
        background: #fff;
        border: 1px solid #eee;
        border-radius: 12px;
        padding: 14px 18px;
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
        margin-bottom: 22px;
    }
    .filter-bar .form-select,
    .filter-bar .form-control {
        font-family: 'Outfit', sans-serif;
        font-size: 13px;
        border-color: #e5e5e5;
        border-radius: 8px;
        height: 38px;
    }
    .filter-bar .form-select:focus,
    .filter-bar .form-control:focus {
        border-color: var(--brand);
        box-shadow: 0 0 0 3px rgba(232,78,58,.1);
    }
    .btn-filter {
        height: 38px;
        padding: 0 18px;
        border-radius: 8px;
        font-family: 'Outfit', sans-serif;
        font-size: 13px;
        font-weight: 600;
        border: none;
        background: var(--brand);
        color: #fff;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 6px;
        transition: background .15s;
        text-decoration: none;
    }
    .btn-filter:hover { background: var(--brand-dark); color: #fff; }
    .btn-filter-reset {
        height: 38px;
        padding: 0 16px;
        border-radius: 8px;
        font-family: 'Outfit', sans-serif;
        font-size: 13px;
        font-weight: 500;
        border: 1px solid #ddd;
        background: #fff;
        color: #666;
        cursor: pointer;
        transition: background .15s;
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .btn-filter-reset:hover { background: #f5f5f5; color: #333; }

    /* ─── Empty state ─── */
    .empty-box {
        background: #fff;
        border: 1px solid #eee;
        border-radius: 14px;
        padding: 60px 20px;
        text-align: center;
        color: #bbb;
    }
    .empty-box i  { font-size: 40px; display: block; margin-bottom: 12px; }
    .empty-box p  { font-size: 14px; margin: 0; }
</style>
@endpush

@section('content')

{{-- ── Header ── --}}
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h5 class="fw-bold mb-1">Pelatihan Saya</h5>
        <p class="text-muted small mb-0">Daftar pelatihan yang Anda ampu</p>
    </div>
</div>

{{-- ── Filter Bar ── --}}
<form method="GET" action="{{ route('instruktur.pelatihan.index') }}" class="filter-bar">
    <i class="bi bi-funnel text-muted" style="font-size:16px;flex-shrink:0"></i>

    <select name="status" class="form-select" style="width:auto;min-width:140px">
        <option value="">Semua Status</option>
        <option value="tersedia" {{ request('status') === 'tersedia' ? 'selected' : '' }}>Tersedia</option>
        <option value="sedang berlangsung"    {{ request('status') === 'sedang berlangsung'    ? 'selected' : '' }}>Sedang Berlangsung</option>
        <option value="selesai"  {{ request('status') === 'selesai'  ? 'selected' : '' }}>Selesai</option>
    </select>

    <button type="submit" class="btn-filter">
        <i class="bi bi-search"></i> Filter
    </button>

    @if(request()->hasAny(['status']))
    <a href="{{ route('instruktur.pelatihan.index') }}" class="btn-filter-reset">
        <i class="bi bi-x-circle"></i> Reset
    </a>
    @endif

    <span class="ms-auto text-muted small">
        {{ $pelatihan->total() }} pelatihan ditemukan
    </span>
</form>

{{-- ── Card Grid ── --}}
@if($pelatihan->isEmpty())
    <div class="empty-box">
        <i class="bi bi-journal-x"></i>
        <p>Belum ada pelatihan yang Anda ampu.</p>
    </div>
@else
<div class="row g-4">
    @foreach($pelatihan as $p)
    <div class="col-12 col-md-6 col-xl-4">
        <div class="pelatihan-card">

            {{-- Top: kode + badge status --}}
            <div class="card-top">
                <span class="kode-pill">{{ $p->kode_pelatihan }}</span>
                <span class="badge-{{ $p->status }}">{{ ucfirst($p->status) }}</span>
            </div>

            {{-- Nama & meta --}}
            <h6 class="card-title">{{ $p->nama_pelatihan }}</h6>
            <div class="card-meta">
                <span>{{ $p->kategori?->nama_kategori ?? '-' }}</span>
                @if($p->tgl_mulai && $p->tgl_selesai)
                <span>
                    {{ \Carbon\Carbon::parse($p->tgl_mulai)->translatedFormat('j M Y') }}
                    &ndash;
                    {{ \Carbon\Carbon::parse($p->tgl_selesai)->translatedFormat('j M Y') }}
                </span>
                @endif
            </div>

            {{-- Stats: Sesi / Peserta / Sertif --}}
            <div class="card-stats">
                <div class="cs-item">
                    <span class="cs-label">Sesi</span>
                    <span class="cs-value">{{ $p->sesi_pelatihan_count ?? 0 }}</span>
                </div>
                <div class="cs-item">
                    <span class="cs-label">Peserta</span>
                    <span class="cs-value">
                        {{ $p->pendaftaran_count ?? 0 }}/{{ $p->kuota ?? '–' }}
                    </span>
                </div>
                <div class="cs-item">
                    <span class="cs-label">Sertif</span>
                    <span class="cs-value">{{ $p->sertifikat_count ?? 0 }}</span>
                </div>
            </div>

            <hr class="card-divider">

            {{-- Tombol Buka Logbook --}}
            <a href="{{ route('instruktur.logbook.index', ['pelatihan' => $p->id_pelatihan]) }}"
               class="btn-logbook">
                <i class="bi bi-clipboard-check"></i>
                Buka Logbook
            </a>

        </div>
    </div>
    @endforeach
</div>

{{-- ── Pagination ── --}}
@if($pelatihan->hasPages())
<div class="d-flex justify-content-center mt-4">
    {{ $pelatihan->links('pagination::bootstrap-5') }}
</div>
@endif

@endif

@endsection