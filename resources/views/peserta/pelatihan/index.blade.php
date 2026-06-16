@extends('layouts.peserta')

@section('title', 'Katalog Pelatihan')
@section('page-title', 'Katalog Pelatihan')

@push('styles')
<style>
    /* ─── FILTER BAR ─── */
    .filter-bar {
        background: #fff;
        border: 1px solid #eee;
        border-radius: 12px;
        padding: 16px 20px;
        margin-bottom: 24px;
        display: flex;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
    }
    .filter-bar .input-search {
        flex: 1;
        min-width: 200px;
        position: relative;
    }
    .filter-bar .input-search i {
        position: absolute;
        left: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: #aaa;
        font-size: 15px;
        pointer-events: none;
    }
    .filter-bar .input-search input {
        width: 100%;
        padding: 8px 12px 8px 36px;
        border: 1.5px solid #e5e5e5;
        border-radius: 8px;
        font-family: 'Outfit', sans-serif;
        font-size: 13.5px;
        color: #333;
        outline: none;
        transition: border-color .15s;
    }
    .filter-bar .input-search input:focus { border-color: var(--brand); }

    .filter-bar select {
        padding: 8px 12px;
        border: 1.5px solid #e5e5e5;
        border-radius: 8px;
        font-family: 'Outfit', sans-serif;
        font-size: 13.5px;
        color: #555;
        background: #fff;
        outline: none;
        cursor: pointer;
        transition: border-color .15s;
    }
    .filter-bar select:focus { border-color: var(--brand); }

    .btn-filter {
        padding: 8px 20px;
        background: var(--brand);
        color: #fff;
        border: none;
        border-radius: 8px;
        font-family: 'Outfit', sans-serif;
        font-size: 13.5px;
        font-weight: 600;
        cursor: pointer;
        transition: background .15s;
        white-space: nowrap;
    }
    .btn-filter:hover { background: var(--brand-dark); }

    .btn-reset {
        padding: 8px 16px;
        background: #f3f4f6;
        color: #555;
        border: none;
        border-radius: 8px;
        font-family: 'Outfit', sans-serif;
        font-size: 13.5px;
        font-weight: 500;
        cursor: pointer;
        transition: background .15s;
        text-decoration: none;
        white-space: nowrap;
    }
    .btn-reset:hover { background: #e5e7eb; color: #333; }

    /* ─── CARD GRID ─── */
    .katalog-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
    }
    @media (max-width: 768px) { .katalog-grid { grid-template-columns: 1fr; } }

    /* ─── TRAINING CARD ─── */
    .training-card {
        background: #fff;
        border: 1px solid #eee;
        border-radius: 14px;
        padding: 22px;
        display: flex;
        flex-direction: column;
        gap: 12px;
        transition: box-shadow .2s, transform .2s;
    }
    .training-card:hover { box-shadow: 0 8px 28px rgba(0,0,0,.09); transform: translateY(-2px); }

    /* Card Header */
    .card-top {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 8px;
    }
    .card-kode {
        font-family: monospace;
        font-size: 11px;
        color: #888;
        background: #f3f4f6;
        padding: 2px 8px;
        border-radius: 4px;
        flex-shrink: 0;
    }

    /* Badge Status */
    .status-badge {
        font-size: 11px;
        font-weight: 600;
        padding: 3px 10px;
        border-radius: 999px;
        white-space: nowrap;
        flex-shrink: 0;
    }
    .status-tersedia { background: #dcfce7; color: #15803d; }
    .status-selesai  { background: #f3f4f6; color: #374151; }
    .status-penuh    { background: #fee2e2; color: #991b1b; }

    /* Card Body */
    .card-nama {
        font-size: 17px;
        font-weight: 700;
        color: #1a1a2e;
        margin: 0;
        line-height: 1.3;
    }
    .card-instruktur {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 12.5px;
        color: #888;
        margin-top: -4px;
    }
    .card-instruktur i { font-size: 13px; }
    .card-deskripsi {
        font-size: 13px;
        color: #666;
        line-height: 1.55;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    /* Meta row */
    .card-meta {
        display: flex;
        align-items: center;
        gap: 16px;
        flex-wrap: wrap;
    }
    .meta-item {
        display: flex;
        align-items: center;
        gap: 5px;
        font-size: 12px;
        color: #888;
    }
    .meta-item i { font-size: 13px; }

    /* Progress bar */
    .progress-wrap { display: flex; flex-direction: column; gap: 4px; }
    .progress-bar-track {
        height: 5px;
        background: #f0f0f0;
        border-radius: 999px;
        overflow: hidden;
    }
    .progress-bar-fill {
        height: 100%;
        border-radius: 999px;
        background: var(--brand);
        transition: width .4s ease;
    }
    .progress-bar-fill.full { background: #9ca3af; }
    .progress-label {
        font-size: 11px;
        color: #bbb;
        text-align: right;
    }

    /* Card Footer — Tombol */
    .card-footer-btn { margin-top: auto; }

    .btn-daftar {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        width: 100%;
        padding: 11px;
        background: var(--brand);
        color: #fff;
        border: none;
        border-radius: 9px;
        font-family: 'Outfit', sans-serif;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        text-decoration: none;
        transition: background .15s;
    }
    .btn-daftar:hover { background: var(--brand-dark); color: #fff; }

    .btn-daftar-disabled {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        width: 100%;
        padding: 11px;
        background: #f3f4f6;
        color: #9ca3af;
        border: none;
        border-radius: 9px;
        font-family: 'Outfit', sans-serif;
        font-size: 14px;
        font-weight: 600;
        cursor: not-allowed;
    }

    .btn-terdaftar {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        width: 100%;
        padding: 11px;
        background: #f0fdf4;
        color: #15803d;
        border: 1.5px solid #bbf7d0;
        border-radius: 9px;
        font-family: 'Outfit', sans-serif;
        font-size: 14px;
        font-weight: 600;
        cursor: default;
    }

    /* ─── EMPTY STATE ─── */
    .empty-katalog {
        grid-column: 1 / -1;
        text-align: center;
        padding: 60px 20px;
        color: #bbb;
    }
    .empty-katalog i { font-size: 48px; display: block; margin-bottom: 12px; opacity: .4; }
    .empty-katalog p { font-size: 14px; }

    /* ─── PAGINATION ─── */
    .paginasi { margin-top: 28px; }
</style>
@endpush

@section('content')

{{-- ── Heading ── --}}
<div class="mb-4">
    <h5 class="fw-bold mb-1">Katalog Pelatihan</h5>
    <p class="text-muted small mb-0">Temukan pelatihan yang sesuai dengan kebutuhanmu dan daftar sekarang.</p>
</div>

{{-- ── Filter Bar ── --}}
<form method="GET" action="{{ route('peserta.pelatihan.index') }}">
    <div class="filter-bar">
        <div class="input-search">
            <i class="bi bi-search"></i>
            <input type="text" name="search"
                   placeholder="Cari nama pelatihan…"
                   value="{{ request('search') }}">
        </div>

        <select name="kategori">
            <option value="">Semua Kategori</option>
        @foreach($kategori as $kat)
            <option value="{{ $kat->id_kategori }}" 
                @selected(request('kategori') == $kat->id_kategori)>
                {{ $kat->nama_kategori }}
            </option>
            @endforeach
        </select>

        <button type="submit" class="btn-filter">
            <i class="bi bi-funnel-fill me-1"></i> Filter
        </button>

        @if(request()->anyFilled(['search', 'kategori']))
            <a href="{{ route('peserta.pelatihan.index') }}" class="btn-reset">
                <i class="bi bi-x-circle me-1"></i> Reset
            </a>
        @endif
    </div>
</form>

{{-- ── Grid Katalog ── --}}
<div class="katalog-grid">

    @forelse($pelatihan as $p)
        @php
            $kuota     = $p->kuota ?? 0;
            $terisi    = $p->pendaftaran_count;
            $sisa      = max(0, $kuota - $terisi);
            $persen    = $kuota > 0 ? min(100, round(($terisi / $kuota) * 100)) : 0;
            $sudah     = in_array($p->id_pelatihan, $sudahDaftar);
            $sedangBerlangsung = $p->status === 'sedang berlangsung';
            $selesai   = $p->status === 'selesai';
            $bisaDaftar = !$sudah && !$sedangBerlangsung && !$selesai;
        @endphp

        <div class="training-card">

            {{-- Baris atas: kode + badge status --}}
            <div class="card-top">
                <span class="card-kode">{{ $p->kode_pelatihan }}</span>
                <span class="status-badge status-{{ $p->status }}">{{ $p->status }}</span>
            </div>

            {{-- Nama & instruktur --}}
            <div>
                <h6 class="card-nama">{{ $p->nama_pelatihan }}</h6>
                <div class="card-instruktur">
                    <i class="bi bi-person-fill"></i>
                    {{ $p->instruktur?->nama_lengkap ?? $p->instruktur?->nama ?? '-' }}
                </div>
            </div>

            {{-- Deskripsi --}}
            <p class="card-deskripsi">{{ $p->deskripsi }}</p>

            {{-- Meta: tanggal & sisa kursi --}}
            <div class="card-meta">
                @if($p->tgl_mulai && $p->tgl_selesai)
                <div class="meta-item">
                    <i class="bi bi-calendar3"></i>
                    {{ \Carbon\Carbon::parse($p->tgl_mulai)->translatedFormat('j M Y') }}
                    &ndash;
                    {{ \Carbon\Carbon::parse($p->tgl_selesai)->translatedFormat('j M Y') }}
                </div>
                @endif
                <div class="meta-item">
                    <i class="bi bi-people"></i>
                    Sisa {{ $sisa }}/{{ $kuota }} kursi
                </div>
            </div>

            {{-- Progress bar --}}
            <div class="progress-wrap">
                <div class="progress-bar-track">
                    <div class="progress-bar-fill {{ $persen >= 100 ? 'full' : '' }}"
                         style="width:{{ $persen }}%"></div>
                </div>
                <div class="progress-label">{{ $terisi }}/{{ $kuota }} terdaftar</div>
            </div>

            {{-- Tombol --}}
            <div class="card-footer-btn">
                @if($sudah)
                    <div class="btn-terdaftar">
                        <i class="bi bi-check-circle-fill"></i> Sudah Terdaftar
                    </div>
                @elseif($selesai)
                    <div class="btn-daftar-disabled">
                        <i class="bi bi-slash-circle"></i> Pelatihan Selesai
                    </div>
                @elseif($sedangBerlangsung)
                    <div class="btn-daftar-disabled">
                        <i class="bi bi-x-circle"></i> Pendaftaran Ditutup
                    </div>
                @else
                    <a href="{{ route('peserta.pendaftaran.index', $p->id_pelatihan) }}"
                       class="btn-daftar">
                        <i class="bi bi-person-plus-fill"></i> Daftar Sekarang
                    </a>
                @endif
            </div>

        </div>
    @empty
        <div class="empty-katalog">
            <i class="bi bi-journal-x"></i>
            <p>Tidak ada pelatihan yang tersedia saat ini.</p>
        </div>
    @endforelse

</div>

{{-- ── Pagination ── --}}
@if($pelatihan->hasPages())
<div class="paginasi d-flex justify-content-center">
    {{ $pelatihan->links('pagination::bootstrap-5') }}
</div>
@endif

@endsection