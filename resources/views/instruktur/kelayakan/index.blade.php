@extends('layouts.instruktur')

@section('title', 'Status Kelayakan')
@section('page-title', 'Status Kelayakan')

@push('styles')
<style>
    /* ─── FILTER BAR ─── */
    .filter-bar {
        background: #fff;
        border: 1px solid #eee;
        border-radius: 12px;
        padding: 16px 20px;
        display: flex;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
        margin-bottom: 20px;
    }
    .filter-bar .form-select {
        font-family: 'Outfit', sans-serif;
        font-size: 13px;
        border-color: #e5e5e5;
        border-radius: 8px;
        height: 40px;
        min-width: 240px;
        cursor: pointer;
        width: auto;
    }
    .filter-bar .form-select:focus {
        border-color: var(--brand);
        box-shadow: 0 0 0 3px rgba(232,78,58,.1);
    }

    /* ─── TABEL KELAYAKAN ─── */
    .tabel-kelayakan {
        background: #fff;
        border: 1px solid #eee;
        border-radius: 12px;
        overflow: hidden;
    }
    .tabel-kelayakan table {
        width: 100%;
        border-collapse: collapse;
        font-size: 13px;
    }
    .tabel-kelayakan thead th {
        padding: 11px 20px;
        text-align: left;
        font-size: 11px;
        font-weight: 700;
        color: #888;
        text-transform: uppercase;
        letter-spacing: .05em;
        background: #f9fafb;
        border-bottom: 1px solid #f0f0f0;
    }
    .tabel-kelayakan tbody tr {
        border-bottom: 1px solid #f5f5f5;
        transition: background .1s;
    }
    .tabel-kelayakan tbody tr:last-child { border-bottom: none; }
    .tabel-kelayakan tbody tr:hover { background: #fafafa; }
    .tabel-kelayakan tbody td {
        padding: 14px 20px;
        color: #444;
        vertical-align: middle;
    }

    /* ─── PESERTA ─── */
    .peserta-name  { font-weight: 600; color: #1a1a1a; font-size: 13px; }

    /* ─── PROGRESS BAR ─── */
    .persen-wrap {
        display: flex;
        align-items: center;
        gap: 10px;
        min-width: 180px;
    }
    .progress-track {
        flex: 1;
        height: 6px;
        background: #e5e5e5;
        border-radius: 999px;
        overflow: hidden;
    }
    .progress-fill {
        height: 100%;
        border-radius: 999px;
        transition: width .4s ease;
    }
    .fill-green { background: #22c55e; }
    .fill-red   { background: #ef4444; }
    .persen-text {
        font-size: 13px;
        font-weight: 600;
        color: #444;
        white-space: nowrap;
        min-width: 36px;
        text-align: right;
    }

    /* ─── BADGE STATUS KELULUSAN ─── */
    .badge-status {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 14px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 600;
        white-space: nowrap;
    }
    .badge-lulus {
        background: #f0fdf4;
        color: #15803d;
        border: 1px solid #bbf7d0;
    }
    .badge-tidak-lulus {
        background: #fef2f2;
        color: #b91c1c;
        border: 1px solid #fecaca;
    }

    /* ─── INPUT CATATAN ─── */
    .input-catatan {
        width: 100%;
        max-width: 240px;
        border: 1px solid #e5e5e5;
        border-radius: 8px;
        padding: 7px 12px;
        font-family: 'Outfit', sans-serif;
        font-size: 12px;
        color: #555;
        background: #fafafa;
        transition: border-color .15s, background .15s;
    }
    .input-catatan::placeholder { color: #ccc; }
    .input-catatan:focus {
        outline: none;
        border-color: var(--brand);
        background: #fff;
        box-shadow: 0 0 0 3px rgba(232,78,58,.08);
    }

    /* ─── BTN SIMPAN PER BARIS ─── */
    .btn-simpan-row {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 7px 16px;
        border: 1.5px solid var(--brand);
        border-radius: 8px;
        background: #fff;
        color: var(--brand);
        font-family: 'Outfit', sans-serif;
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
        transition: background .15s, color .15s;
        white-space: nowrap;
    }
    .btn-simpan-row:hover { background: var(--brand); color: #fff; }
    .btn-simpan-row i { font-size: 13px; }

    /* ─── EMPTY STATE ─── */
    .empty-box {
        background: #fff;
        border: 1px solid #eee;
        border-radius: 12px;
        padding: 60px 20px;
        text-align: center;
        color: #bbb;
    }
    .empty-box i { font-size: 40px; display: block; margin-bottom: 12px; }
    .empty-box p { font-size: 14px; margin: 0; }

    /* ─── ALERT ─── */
    .alert-custom {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 10px 16px;
        border-radius: 10px;
        font-size: 13px;
        margin-bottom: 18px;
    }
    .alert-success-custom {
        background: #f0fdf4;
        border: 1px solid #bbf7d0;
        color: #15803d;
    }

    /* ─── INFO PELATIHAN DIPILIH ─── */
    .info-pelatihan {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 10px 0 18px;
        font-size: 12px;
        color: #888;
    }
    .info-pelatihan strong { color: #333; }
</style>
@endpush

@section('content')

{{-- ── Header ── --}}
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h5 class="fw-bold mb-1">Status Kelayakan Peserta</h5>
        <p class="text-muted small mb-0">Nilai kelayakan sertifikasi peserta berdasarkan kehadiran</p>
    </div>
</div>

{{-- ── Alert sukses ── --}}
@if(session('success'))
<div class="alert-custom alert-success-custom">
    <i class="bi bi-check-circle-fill"></i>
    {{ session('success') }}
</div>
@endif

{{-- ── Dropdown Pilih Pelatihan ── --}}
<div class="filter-bar">
    <i class="bi bi-journal-bookmark text-muted" style="font-size:16px;flex-shrink:0"></i>
    <select id="sel-pelatihan" class="form-select"
            onchange="window.location.href='{{ route('instruktur.kelayakan.index') }}?pelatihan_id='+this.value">
        <option value="">— Pilih Pelatihan —</option>
        @foreach($pelatihan as $p)
        <option value="{{ $p->id_pelatihan }}"
                {{ $pelatihanDipilih?->id_pelatihan == $p->id_pelatihan ? 'selected' : '' }}>
            {{ $p->nama_pelatihan }} ({{ $p->kode_pelatihan }})
        </option>
        @endforeach
    </select>

    @if($pelatihanDipilih)
    <span class="text-muted small ms-2">
        <i class="bi bi-people me-1"></i>
        {{ $pesertaList->count() }} peserta terdaftar
    </span>
    @endif
</div>

{{-- ══════════════════════════════════════════════════
     TABEL KELAYAKAN
══════════════════════════════════════════════════ --}}
@if(!$pelatihanDipilih)

    <div class="empty-box">
        <i class="bi bi-shield-check"></i>
        <p>Pilih pelatihan untuk melihat dan menilai kelayakan peserta.</p>
    </div>

@elseif($pesertaList->isEmpty())

    <div class="empty-box">
        <i class="bi bi-people"></i>
        <p>Belum ada peserta yang diterima di pelatihan ini.</p>
    </div>

@else

<div class="tabel-kelayakan">
    <table>
        <thead>
            <tr>
                <th style="width:220px">Peserta</th>
                <th style="width:220px">Persen Hadir</th>
                <th style="width:160px">Status Kelulusan</th>
                <th>Catatan Instruktur</th>
                <th style="width:100px;text-align:center">Simpan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pesertaList as $i => $item)
            @php
                $kualifikasi = $item['kualifikasi'];
                $persen      = $item['persen_hadir'];
                $lulus       = $persen >= 80;
                $catatanAda  = $kualifikasi?->catatan ?? '';
                $fillClass   = $lulus ? 'fill-green' : 'fill-red';
            @endphp
            <tr>
                {{-- Nama Peserta --}}
                <td>
                    <div class="peserta-name">{{ $item['peserta']->nama }}</div>
                    <div style="font-size:11px;color:#aaa">{{ $item['peserta']->email }}</div>
                </td>

                {{-- Persen Hadir + Progress Bar --}}
                <td>
                    <div class="persen-wrap">
                        <div class="progress-track">
                            <div class="progress-fill {{ $fillClass }}"
                                 style="width:{{ $persen }}%"></div>
                        </div>
                        <span class="persen-text">{{ $persen }}%</span>
                    </div>
                    <div style="font-size:10px;color:#bbb;margin-top:3px">
                        {{ $item['hadir'] }}/{{ $item['total_sesi'] }} sesi hadir
                    </div>
                </td>

                {{-- Status Kelulusan: otomatis dari persen hadir --}}
                <td>
                    <span class="badge-status {{ $lulus ? 'badge-lulus' : 'badge-tidak-lulus' }}">
                        <i class="bi {{ $lulus ? 'bi-check-circle-fill' : 'bi-x-circle-fill' }}"></i>
                        {{ $lulus ? 'Lulus' : 'Tidak Lulus' }}
                    </span>
                </td>

                {{-- Catatan --}}
                <td>
                    <input type="text"
                           id="catatan-{{ $i }}"
                           class="input-catatan"
                           placeholder="catatan penilaian..."
                           value="{{ $catatanAda }}">
                </td>

                {{-- Tombol Simpan (form individual POST) --}}
                <td style="text-align:center">
                    <form method="POST"
                        action="{{ route('instruktur.kelayakan.simpan', $item['pendaftaran']->id_pendaftaran) }}"
                        id="form-simpan-{{ $i }}">
                        @csrf
                        <input type="hidden" name="persen_hadir" value="{{ $persen }}">
                        <input type="hidden" name="catatan" id="hidden-catatan-{{ $i }}" value="{{ $catatanAda }}">

                        <button type="button" class="btn-simpan-row" onclick="submitBaris({{ $i }})">
                            <i class="bi bi-check-lg"></i> Simpan
                        </button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

@endif

@endsection

@push('scripts')
<script>
    // ── Submit form per baris ────────────────────────────────────────
    function submitBaris(rowIdx) {
        // Sinkronkan catatan dari input teks ke hidden field
        const catatan = document.getElementById('catatan-' + rowIdx).value;
        document.getElementById('hidden-catatan-' + rowIdx).value = catatan;

        // Submit form baris ini
        document.getElementById('form-simpan-' + rowIdx).submit();
    }

</script>
@endpush