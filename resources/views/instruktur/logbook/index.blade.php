@extends('layouts.instruktur')

@section('title', 'Logbook Kehadiran')
@section('page-title', 'Logbook Kehadiran')

@push('styles')
<style>
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
        min-width: 220px;
        cursor: pointer;
    }
    .filter-bar .form-select:focus {
        border-color: var(--brand);
        box-shadow: 0 0 0 3px rgba(232,78,58,.1);
    }

    .btn-simpan {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 0 22px;
        height: 40px;
        background: var(--brand);
        color: #fff;
        border: none;
        border-radius: 8px;
        font-family: 'Outfit', sans-serif;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        transition: background .15s;
        white-space: nowrap;
    }
    .btn-simpan:hover { background: var(--brand-dark); }
    .btn-simpan i { font-size: 15px; }

    .sesi-info {
        background: #fff;
        border: 1px solid #eee;
        border-radius: 12px;
        padding: 18px 22px;
        margin-bottom: 4px;
    }
    .sesi-info h6 {
        font-size: 15px;
        font-weight: 700;
        color: #1a1a1a;
        margin: 0 0 6px;
    }
    .sesi-meta {
        display: flex;
        align-items: center;
        gap: 16px;
        flex-wrap: wrap;
    }
    .sesi-meta span {
        display: flex;
        align-items: center;
        gap: 5px;
        font-size: 12px;
        color: #888;
    }
    .sesi-meta i { font-size: 13px; }

    .tabel-kehadiran {
        background: #fff;
        border: 1px solid #eee;
        border-radius: 12px;
        overflow: hidden;
    }
    .tabel-kehadiran table {
        width: 100%;
        border-collapse: collapse;
        font-size: 13px;
    }
    .tabel-kehadiran thead th {
        padding: 11px 18px;
        text-align: left;
        font-size: 11px;
        font-weight: 700;
        color: #888;
        text-transform: uppercase;
        letter-spacing: .05em;
        background: #f9fafb;
        border-bottom: 1px solid #f0f0f0;
    }
    .tabel-kehadiran tbody tr {
        border-bottom: 1px solid #f5f5f5;
        transition: background .1s;
    }
    .tabel-kehadiran tbody tr:last-child { border-bottom: none; }
    .tabel-kehadiran tbody tr:hover { background: #fafafa; }
    .tabel-kehadiran tbody td {
        padding: 13px 18px;
        color: #444;
        vertical-align: middle;
    }

    td.td-no { width: 40px; color: #bbb; font-size: 13px; }

    .peserta-name { font-weight: 600; color: #222; font-size: 13px; }
    .peserta-email { font-size: 11px; color: #aaa; margin-top: 1px; }

    .radio-group {
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .radio-item {
        display: flex;
        align-items: center;
        gap: 0;
    }
    .radio-item input[type="radio"] { display: none; }

    .radio-item label {
        padding: 5px 13px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 500;
        cursor: pointer;
        transition: background .15s, color .15s;
        border: 1.5px solid transparent;
        white-space: nowrap;
    }

    .radio-item .radio-circle {
        width: 16px;
        height: 16px;
        border-radius: 50%;
        border: 1.5px solid #ddd;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin-right: 6px;
        flex-shrink: 0;
        transition: border-color .15s;
        cursor: pointer;
    }
    .radio-item .radio-circle::after {
        content: '';
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: transparent;
        transition: background .15s;
    }

    .radio-hadir label  { color: #16a34a; }
    .radio-hadir input:checked ~ label { background: #dcfce7; color: #15803d; border-color: #86efac; }
    .radio-hadir input:checked ~ .radio-circle,
    .radio-hadir .radio-circle:has(~ input:checked) { border-color: #16a34a; }

    .radio-izin label { color: #2563eb; }
    .radio-izin input:checked ~ label { background: #dbeafe; color: #1d4ed8; border-color: #93c5fd; }

    .radio-tidak label { color: #dc2626; }
    .radio-tidak input:checked ~ label { background: #fee2e2; color: #b91c1c; border-color: #fca5a5; }

    .radio-wrap {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        cursor: pointer;
    }
    .radio-wrap input[type="radio"] {
        display: none;
    }
    .radio-dot {
        width: 16px;
        height: 16px;
        border-radius: 50%;
        border: 1.5px solid #ccc;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        transition: border-color .15s;
    }
    .radio-dot::after {
        content: '';
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: transparent;
        transition: background .15s;
    }
    .radio-pill {
        font-size: 12px;
        font-weight: 500;
        padding: 4px 11px;
        border-radius: 999px;
        border: 1.5px solid transparent;
        transition: all .15s;
        line-height: 1;
    }

    .radio-wrap.hadir input:checked + .radio-dot { border-color: #16a34a; }
    .radio-wrap.hadir input:checked + .radio-dot::after { background: #16a34a; }
    .radio-wrap.hadir.selected .radio-dot { border-color: #16a34a; }
    .radio-wrap.hadir.selected .radio-dot::after { background: #16a34a; }
    .radio-wrap.hadir.selected .radio-pill { background: #dcfce7; color: #15803d; border-color: #86efac; }
    .radio-wrap.hadir .radio-pill { color: #16a34a; }

    .radio-wrap.izin.selected .radio-dot { border-color: #2563eb; }
    .radio-wrap.izin.selected .radio-dot::after { background: #2563eb; }
    .radio-wrap.izin.selected .radio-pill { background: #dbeafe; color: #1d4ed8; border-color: #93c5fd; }
    .radio-wrap.izin .radio-pill { color: #2563eb; }

    .radio-wrap.tidak.selected .radio-dot { border-color: #dc2626; }
    .radio-wrap.tidak.selected .radio-dot::after { background: #dc2626; }
    .radio-wrap.tidak.selected .radio-pill { background: #fee2e2; color: #b91c1c; border-color: #fca5a5; }
    .radio-wrap.tidak .radio-pill { color: #dc2626; }

    .input-catatan {
        width: 100%;
        max-width: 220px;
        border: 1px solid #e5e5e5;
        border-radius: 8px;
        padding: 7px 12px;
        font-family: 'Outfit', sans-serif;
        font-size: 12px;
        color: #555;
        background: #fafafa;
        transition: border-color .15s;
    }
    .input-catatan::placeholder { color: #ccc; }
    .input-catatan:focus {
        outline: none;
        border-color: var(--brand);
        background: #fff;
        box-shadow: 0 0 0 3px rgba(232,78,58,.08);
    }

    .badge-diisi {
        background: #dcfce7;
        color: #15803d;
        font-size: 10px;
        font-weight: 700;
        padding: 2px 8px;
        border-radius: 999px;
    }
    .badge-belum {
        background: #f3f4f6;
        color: #9ca3af;
        font-size: 10px;
        font-weight: 600;
        padding: 2px 8px;
        border-radius: 999px;
    }

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
</style>
@endpush

@section('content')

<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h5 class="fw-bold mb-1">Logbook Kehadiran</h5>
        <p class="text-muted small mb-0">Input kehadiran peserta per sesi pelatihan</p>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success rounded-3 d-flex align-items-center gap-2 mb-4 py-2"
     style="font-size:13px;border:1px solid #bbf7d0">
    <i class="bi bi-check-circle-fill text-success"></i>
    {{ session('success') }}
</div>
@endif

<form method="POST"
      action="{{ $sesiDipilih ? route('instruktur.logbook.simpan', $sesiDipilih->id_sesi) : '#' }}"
      id="form-logbook">
@csrf
@if($sesiDipilih) @method('POST') @endif

    <div class="card p-4 mb-3">
        <div class="d-flex gap-3 flex wrap">
        <select name="_pelatihan_id" id="sel-pelatihan" class="form-select"
                onchange="gantiPelatihan(this.value)">
            <option value="">— Pilih Pelatihan —</option>
            @foreach($pelatihan as $p)
            <option value="{{ $p->id_pelatihan }}"
                {{ $pelatihanDipilih?->id_pelatihan == $p->id_pelatihan ? 'selected' : '' }}>
                {{ $p->nama_pelatihan }} ({{ $p->kode_pelatihan }})
            </option>
            @endforeach
        </select>

        <select name="sesi_id" id="sel-sesi" class="form-select"
                onchange="gantiSesi(this.value)"
                {{ !$pelatihanDipilih ? 'disabled' : '' }}>
            <option value="">— Pilih Sesi —</option>
            @if($pelatihanDipilih)
                @foreach($sesiList as $s)
                <option value="{{ $s->id_sesi }}"
                    {{ $sesiDipilih?->id_sesi == $s->id_sesi ? 'selected' : '' }}>
                    Sesi {{ $loop->iteration }}: {{ $s->judul_sesi ?? 'Sesi '.$loop->iteration }}
                </option>
                @endforeach
            @endif
        </select>

        @if($sesiDipilih)
        <button type="submit" class="btn-simpan ms-auto">
            <i class="bi bi-floppy2-fill"></i> Simpan Kehadiran
        </button>
        @endif
        </div>
    </div>

    @if($sesiDipilih)
    <div class="sesi-info mb-3">
        <h6>{{ $sesiDipilih->judul_sesi ?? 'Sesi Pelatihan' }}</h6>
        <div class="sesi-meta">
            <span>
                <i class="bi bi-calendar3"></i>
                {{ \Carbon\Carbon::parse($sesiDipilih->tanggal)->translatedFormat('j F Y') }}
            </span>
            @if($sesiDipilih->waktu_mulai && $sesiDipilih->waktu_selesai)
            <span>
                <i class="bi bi-clock"></i>
                {{ \Carbon\Carbon::parse($sesiDipilih->waktu_mulai)->format('H:i') }}
                –
                {{ \Carbon\Carbon::parse($sesiDipilih->waktu_selesai)->format('H:i') }}
            </span>
            @endif
            @if($sesiDipilih->lokasi)
            <span>
                <i class="bi bi-geo-alt"></i>
                {{ $sesiDipilih->lokasi }}
            </span>
            @endif
            @if(isset($sesiSudahDiisi[$sesiDipilih->id_sesi]))
            <span class="ms-auto">
                <span class="badge-diisi">
                    <i class="bi bi-check2"></i>
                    Sudah diisi ({{ $sesiSudahDiisi[$sesiDipilih->id_sesi] }} peserta)
                </span>
            </span>
            @else
            <span class="ms-auto">
                <span class="badge-belum">Belum diisi</span>
            </span>
            @endif
        </div>
    </div>

    @if($peserta->isEmpty())
    <div class="empty-box">
        <i class="bi bi-people"></i>
        <p>Belum ada peserta yang diterima di pelatihan ini.</p>
    </div>
    @else
    <div class="tabel-kehadiran">
        <table>
            <thead>
                <tr>
                    <th style="width:40px">#</th>
                    <th>Nama Peserta</th>
                    <th>Email</th>
                    <th>Status Kehadiran</th>
                    <th>Catatan</th>
                </tr>
            </thead>
            <tbody>
                @foreach($peserta as $i => $daftar)
                @php
                    $p        = $daftar->peserta;
                    $existing = $logbookAda[$p->id_user] ?? null;
                    $status   = $existing?->status ?? 'hadir'; // default hadir
                    $catatan  = $existing?->catatan ?? '';
                @endphp
                <tr>
                    <td class="td-no">{{ $i + 1 }}</td>

                    <td>
                        <div class="peserta-name">{{ $p->nama }}</div>
                        <div class="peserta-email">{{ $p->email }}</div>
                    </td>

                    <td style="color:#aaa;font-size:12px">{{ $p->email }}</td>

                    <td>
                        <input type="hidden"
                               name="kehadiran[{{ $i }}][peserta_id]"
                               value="{{ $p->id_user }}">

                        <div class="radio-group" id="rg-{{ $i }}">

                            <label class="radio-wrap hadir {{ $status === 'hadir' ? 'selected' : '' }}"
                                   onclick="pilihStatus({{ $i }}, 'hadir', this)">
                                <input type="radio"
                                       name="kehadiran[{{ $i }}][status]"
                                       value="hadir"
                                       {{ $status === 'hadir' ? 'checked' : '' }}>
                                <span class="radio-dot"></span>
                                <span class="radio-pill">hadir</span>
                            </label>

                            <label class="radio-wrap izin {{ $status === 'izin' ? 'selected' : '' }}"
                                   onclick="pilihStatus({{ $i }}, 'izin', this)">
                                <input type="radio"
                                       name="kehadiran[{{ $i }}][status]"
                                       value="izin"
                                       {{ $status === 'izin' ? 'checked' : '' }}>
                                <span class="radio-dot"></span>
                                <span class="radio-pill">izin</span>
                            </label>

                            <label class="radio-wrap tidak {{ $status === 'tidak hadir' ? 'selected' : '' }}"
                                   onclick="pilihStatus({{ $i }}, 'tidak hadir', this)">
                                <input type="radio"
                                       name="kehadiran[{{ $i }}][status]"
                                       value="tidak hadir"
                                       {{ $status === 'tidak hadir' ? 'checked' : '' }}>
                                <span class="radio-dot"></span>
                                <span class="radio-pill">tidak hadir</span>
                            </label>

                        </div>
                    </td>

                    <td>
                        <input type="text"
                               name="kehadiran[{{ $i }}][catatan]"
                               value="{{ $catatan }}"
                               placeholder="catatan..."
                               class="input-catatan">
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif 

    @else
    <div class="empty-box">
        <i class="bi bi-clipboard-check"></i>
        <p>Pilih pelatihan dan sesi untuk mengisi logbook kehadiran.</p>
    </div>
    @endif 

</form>

@endsection

@push('scripts')
<script>
    function gantiPelatihan(pelatihanId) {
        if (!pelatihanId) return;
        window.location.href = '{{ route('instruktur.logbook.index') }}?pelatihan_id=' + pelatihanId;
    }

    function gantiSesi(sesiId) {
        const pelatihanId = document.getElementById('sel-pelatihan').value;
        if (!sesiId) return;
        window.location.href = '{{ route('instruktur.logbook.index') }}'
            + '?pelatihan_id=' + pelatihanId
            + '&sesi_id=' + sesiId;
    }

    function pilihStatus(rowIdx, status, clickedLabel) {
        const group = document.getElementById('rg-' + rowIdx);

        group.querySelectorAll('.radio-wrap').forEach(el => el.classList.remove('selected'));

        clickedLabel.classList.add('selected');

        const radio = clickedLabel.querySelector('input[type="radio"]');
        if (radio) radio.checked = true;
    }

    document.querySelectorAll('.radio-group').forEach(group => {
        const checked = group.querySelector('input[type="radio"]:checked');
        if (checked) {
            checked.closest('.radio-wrap')?.classList.add('selected');
        }
    });
</script>
@endpush