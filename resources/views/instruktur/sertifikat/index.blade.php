@extends('layouts.instruktur')

@section('title', 'Riwayat Sertifikat')
@section('page-title', 'Riwayat Sertifikat')

@push('styles')
<style>
    /* ─── PANEL ─── */
    .panel {
        background: #fff;
        border: 1px solid #eee;
        border-radius: 12px;
        overflow: hidden;
    }

    /* ─── TABEL ─── */
    .tabel-sertif { width: 100%; border-collapse: collapse; font-size: 13px; }
    .tabel-sertif thead th {
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
    .tabel-sertif tbody tr { border-bottom: 1px solid #f5f5f5; transition: background .1s; }
    .tabel-sertif tbody tr:last-child { border-bottom: none; }
    .tabel-sertif tbody tr:hover { background: #fafafa; }
    .tabel-sertif tbody td { padding: 13px 20px; color: #444; vertical-align: middle; }

    /* kode sertifikat */
    .kode-sertif {
        font-family: monospace;
        font-size: 12px;
        background: #f3f4f6;
        color: #444;
        padding: 4px 10px;
        border-radius: 6px;
        font-weight: 600;
        white-space: nowrap;
    }

    /* nama peserta */
    .nama-peserta { font-weight: 600; color: #1a1a1a; }

    /* tanggal */
    .tgl-terbit { font-size: 12px; color: #888; white-space: nowrap; }

    /* diterbitkan oleh */
    .diterbitkan { font-size: 12px; color: #aaa; }

    /* ─── BTN LIHAT ─── */
    .btn-lihat {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 6px 14px;
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
        text-decoration: none;
    }
    .btn-lihat:hover { background: var(--brand); color: #fff; }
    .btn-lihat i { font-size: 13px; }

    /* ─── EMPTY STATE ─── */
    .empty-box {
        padding: 60px 20px;
        text-align: center;
        color: #bbb;
    }
    .empty-box i { font-size: 40px; display: block; margin-bottom: 12px; }
    .empty-box p { font-size: 14px; margin: 0; }

    /* ─── MODAL PREVIEW ─── */
    .modal-preview .modal-content {
        border: 0;
        border-radius: 16px;
        box-shadow: 0 20px 60px rgba(0,0,0,.18);
    }
    .modal-preview .modal-header {
        border-bottom: 1px solid #f0f0f0;
        padding: 16px 22px;
    }
    .modal-preview .modal-title {
        font-size: 14px;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 8px;
        color: #333;
    }
    .modal-preview .modal-footer {
        border-top: 1px solid #f0f0f0;
        padding: 14px 22px;
        gap: 10px;
    }

    /* keterangan di atas preview */
    .preview-hint {
        font-size: 12px;
        color: #aaa;
        margin-bottom: 14px;
        line-height: 1.5;
    }

    /* ─── SERTIFIKAT PREVIEW CARD ─── */
    .sertif-card {
        border: 1.5px solid #e5e5e5;
        border-radius: 10px;
        padding: 28px 32px 22px;
        text-align: center;
        background: #fff;
        position: relative;
        overflow: hidden;
    }
    /* Dekorasi garis atas */
    .sertif-card::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0;
        height: 4px;
        background: linear-gradient(90deg, var(--brand), #f5a623);
    }

    /* Trophy icon */
    .trophy-icon {
        font-size: 36px;
        margin-bottom: 8px;
        display: block;
        color: #f5a623;
    }

    /* Judul sertifikat */
    .sertif-judul {
        font-size: 15px;
        font-weight: 800;
        letter-spacing: .12em;
        text-transform: uppercase;
        color: #1a1a1a;
        margin: 0 0 2px;
    }
    .sertif-subjudul {
        font-size: 9px;
        letter-spacing: .18em;
        text-transform: uppercase;
        color: #aaa;
        margin-bottom: 16px;
    }

    /* divider */
    .sertif-divider {
        border: none;
        border-top: 1px solid #eee;
        margin: 12px auto;
        width: 80%;
    }

    /* Diberikan kepada */
    .sertif-diberikan { font-size: 11px; color: #999; margin-bottom: 6px; }

    /* Nama penerima */
    .sertif-nama {
        font-size: 22px;
        font-style: italic;
        font-weight: 700;
        color: var(--brand);
        margin: 0 0 8px;
        line-height: 1.2;
    }

    /* Kalimat */
    .sertif-kalimat {
        font-size: 11px;
        color: #666;
        margin-bottom: 10px;
        line-height: 1.6;
    }

    /* Nama pelatihan */
    .sertif-pelatihan {
        font-size: 14px;
        font-weight: 700;
        color: #1a1a1a;
        margin-bottom: 6px;
    }

    /* Meta info: tanggal, kehadiran, kode */
    .sertif-meta {
        font-size: 10px;
        color: #aaa;
        margin-bottom: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        flex-wrap: wrap;
    }
    .sertif-meta span + span::before {
        content: '|';
        margin-right: 10px;
        color: #ddd;
    }

    /* QR placeholder */
    .qr-box {
        width: 48px;
        height: 48px;
        border: 1px solid #ddd;
        border-radius: 6px;
        margin: 0 auto 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #ccc;
        font-size: 20px;
    }

    /* Footer tiga kolom: penandatangan | kode | instruktur */
    .sertif-footer {
        display: grid;
        grid-template-columns: 1fr 1fr 1fr;
        gap: 8px;
        padding-top: 12px;
        border-top: 1px solid #f0f0f0;
    }
    .sf-col { text-align: center; }
    .sf-col .sf-main { font-size: 12px; font-weight: 700; color: #222; }
    .sf-col .sf-sub  { font-size: 10px; color: #aaa; margin-top: 2px; }
    .sf-col .sf-kode {
        font-family: monospace;
        font-size: 11px;
        font-weight: 700;
        color: var(--brand);
        background: var(--brand-light);
        padding: 3px 8px;
        border-radius: 4px;
        display: inline-block;
    }

    /* copyright kecil */
    .sertif-copy { font-size: 9px; color: #ccc; margin-top: 10px; }
</style>
@endpush

@section('content')

{{-- ── Header ── --}}
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h5 class="fw-bold mb-1">Riwayat Sertifikat</h5>
        <p class="text-muted small mb-0">Sertifikat yang diterbitkan pada pelatihan Anda</p>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success rounded-3 d-flex align-items-center gap-2 mb-4 py-2"
     style="font-size:13px;border:1px solid #bbf7d0">
    <i class="bi bi-check-circle-fill text-success"></i>
    {{ session('success') }}
</div>
@endif

{{-- ── Tabel Sertifikat ── --}}
<div class="panel">
    <div class="table-responsive">
        <table class="tabel-sertif">
            <thead>
                <tr>
                    <th>Kode Sertifikat</th>
                    <th>Peserta</th>
                    <th>Pelatihan</th>
                    <th>Tanggal Terbit</th>
                    <th>Diterbitkan Oleh</th>
                    <th style="text-align:right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($sertifikat as $s)
                @php
                    $peserta      = $s->pendaftaran?->peserta;
                    $pelatihan    = $s->pendaftaran?->pelatihan;
                    $templateUrl  = $pelatihan?->template_sertifikat
                        ? \Illuminate\Support\Facades\Storage::url($pelatihan->template_sertifikat)
                        : '';
                    $fileUrl      = $s->file
                        ? \Illuminate\Support\Facades\Storage::url($s->file)
                        : '';
                @endphp
                <tr>
                    {{-- Kode --}}
                    <td><span class="kode-sertif">{{ $s->kode_sertifikat }}</span></td>

                    {{-- Peserta --}}
                    <td>
                        <span class="nama-peserta">{{ $peserta?->nama ?? '-' }}</span>
                    </td>

                    {{-- Pelatihan --}}
                    <td style="color:#555;font-size:13px">{{ $pelatihan?->nama_pelatihan ?? '-' }}</td>

                    {{-- Tanggal Terbit --}}
                    <td>
                        <span class="tgl-terbit">
                            {{ \Carbon\Carbon::parse($s->tgl_terbit)->translatedFormat('j M Y') }}
                        </span>
                    </td>

                    {{-- Diterbitkan Oleh --}}
                    <td><span class="diterbitkan">{{ $s->diterbitkan_oleh }}</span></td>

                    {{-- Aksi: Lihat / Preview --}}
                    <td style="text-align:right">
                        <button type="button" class="btn-lihat" onclick="bukaPreview(
                                    '{{ addslashes($s->kode_sertifikat) }}',
                                    '{{ addslashes($s->nomor_sertifikat ?? '-') }}',
                                    '{{ addslashes($peserta?->nama ?? '-') }}',
                                    '{{ addslashes($pelatihan?->nama_pelatihan ?? '-') }}',
                                    '{{ addslashes($pelatihan?->kode_pelatihan ?? '-') }}',
                                    '{{ \Carbon\Carbon::parse($s->tgl_terbit)->translatedFormat('j M Y') }}',
                                    '{{ addslashes($s->diterbitkan_oleh) }}',
                                    '{{ addslashes(Auth::user()->nama) }}',
                                    '{{ $fileUrl }}',
                                    '{{ $templateUrl }}'
                                )">
                            <i class="bi bi-eye"></i> Lihat
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6">
                        <div class="empty-box">
                            <i class="bi bi-award"></i>
                            <p>Belum ada sertifikat yang diterbitkan pada pelatihan Anda.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($sertifikat->hasPages())
    <div class="d-flex justify-content-between align-items-center px-4 py-3"
         style="border-top:1px solid #f0f0f0">
        <small class="text-muted">
            Menampilkan {{ $sertifikat->firstItem() }}–{{ $sertifikat->lastItem() }}
            dari {{ $sertifikat->total() }} sertifikat
        </small>
        {{ $sertifikat->links('pagination::bootstrap-5') }}
    </div>
    @endif
</div>

{{-- ══════════════════════════════════════════════════════
     MODAL PREVIEW SERTIFIKAT
══════════════════════════════════════════════════════ --}}
<div class="modal fade modal-preview" id="modalPreview" tabindex="-1"
     aria-labelledby="modalPreviewLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width:540px">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="modalPreviewLabel">
                    <i class="bi bi-eye" style="color:var(--brand)"></i>
                    Preview Sertifikat
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body px-3 py-3">

                {{-- iframe PDF --}}
                <div id="prev-pdf-wrap"
                    style="border:1px solid #dee2e6; border-radius:10px; overflow:hidden;
                            background:#f5f5f5; height:420px; display:none;">
                    <iframe id="prev-pdf-iframe" src=""
                            style="width:100%;height:100%;border:0;"></iframe>
                </div>

                {{-- Fallback kalau belum ada PDF --}}
                <div id="prev-pdf-fallback"
                    style="height:200px;display:flex;flex-direction:column;
                            align-items:center;justify-content:center;
                            border:2px dashed #e5e5e5;border-radius:10px;
                            background:#fafafa;color:#bbb;">
                    <i class="bi bi-file-earmark-pdf" style="font-size:40px;margin-bottom:8px;"></i>
                    <div style="font-size:13px;">PDF sertifikat belum tersedia</div>
                </div>

                {{-- Info strip di bawah iframe --}}
                <div id="prev-info-strip"
                    style="display:none;margin-top:10px;padding:10px 14px;
                            background:#f9fafb;border:1px solid #f0f0f0;
                            border-radius:8px;font-size:12px;color:#666;">
                    <div class="d-flex justify-content-between flex-wrap gap-2">
                        <span>
                            <i class="bi bi-person me-1 text-muted"></i>
                            <span id="prev-nama-strip">—</span>
                        </span>
                        <span>
                            <i class="bi bi-hash me-1 text-muted"></i>
                            <span id="prev-nomor-strip">—</span>
                        </span>
                        <span>
                            <i class="bi bi-upc-scan me-1 text-muted"></i>
                            <span id="prev-kode-strip">—</span>
                        </span>
                        <span>
                            <i class="bi bi-calendar3 me-1 text-muted"></i>
                            <span id="prev-tgl-strip">—</span>
                        </span>
                    </div>
                </div>

            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary rounded-3 px-4"
                        data-bs-dismiss="modal">Kembali</button>
                <a id="btn-download-pdf" href="#" target="_blank"
                class="btn btn-primary rounded-3 px-4 fw-semibold"
                style="display:none">
                    <i class="bi bi-download me-1"></i> Unduh PDF
                </a>
            </div>

        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    function bukaPreview(kode, nomor, nama, pelatihan, kodePel, tgl, diterbitkan, instruktur, fileUrl, templateUrl) {
    const iframe    = document.getElementById('prev-pdf-iframe');
    const pdfWrap   = document.getElementById('prev-pdf-wrap');
    const fallback  = document.getElementById('prev-pdf-fallback');
    const infoStrip = document.getElementById('prev-info-strip');

    if (fileUrl && fileUrl.trim() !== '') {
        iframe.src              = fileUrl;
        pdfWrap.style.display   = 'block';
        fallback.style.display  = 'none';
        infoStrip.style.display = 'block';
    } else {
        iframe.src              = '';
        pdfWrap.style.display   = 'none';
        fallback.style.display  = 'flex';
        infoStrip.style.display = 'none';
    }

    document.getElementById('prev-nama-strip').textContent  = nama;
    document.getElementById('prev-nomor-strip').textContent = nomor;
    document.getElementById('prev-kode-strip').textContent  = kode;
    document.getElementById('prev-tgl-strip').textContent   = tgl;

    const btnDownload = document.getElementById('btn-download-pdf');
    if (fileUrl && fileUrl.trim() !== '') {
        btnDownload.href         = fileUrl;
        btnDownload.style.display = 'inline-flex';
    } else {
        btnDownload.style.display = 'none';
    }

    new bootstrap.Modal(document.getElementById('modalPreview')).show();
}
</script>
@endpush