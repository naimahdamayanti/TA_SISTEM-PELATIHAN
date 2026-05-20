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
                    $peserta   = $s->pendaftaran?->peserta;
                    $pelatihan = $s->pendaftaran?->pelatihan;
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
                        <button type="button" class="btn-lihat"
                                onclick="bukaPreview(
                                    '{{ addslashes($s->kode_sertifikat) }}',
                                    '{{ addslashes($peserta?->nama ?? '-') }}',
                                    '{{ addslashes($pelatihan?->nama_pelatihan ?? '-') }}',
                                    '{{ addslashes($pelatihan?->kode_pelatihan ?? '-') }}',
                                    '{{ \Carbon\Carbon::parse($s->tgl_terbit)->translatedFormat('j M Y') }}',
                                    '{{ addslashes($s->diterbitkan_oleh) }}',
                                    '{{ addslashes(Auth::user()->nama) }}',
                                    '{{ $s->pendaftaran?->kualifikasiSertifikasi?->persen_hadir ?? 0 }}',
                                    '{{ $s->file ? Storage::url($s->file) : '' }}'
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

            <div class="modal-body px-4 py-3">
                <p class="preview-hint">
                    Tampilan sertifikat yang telah diterbitkan. Kode unik masing-masing peserta tercantum di bagian bawah.
                </p>

                {{-- ── Sertifikat Card ── --}}
                <div class="sertif-card">

                    {{-- Trophy --}}
                    <span class="trophy-icon">🏆</span>

                    {{-- Judul --}}
                    <p class="sertif-judul">Sertifikat Kelulusan</p>
                    <p class="sertif-subjudul">Certificate of Completion</p>

                    <hr class="sertif-divider">

                    <p class="sertif-diberikan">Diberikan kepada</p>
                    <p class="sertif-nama" id="prev-nama">—</p>

                    <p class="sertif-kalimat">
                        telah berhasil menyelesaikan dan dinyatakan<br>
                        lulus dalam program pelatihan
                    </p>

                    <p class="sertif-pelatihan" id="prev-pelatihan">—</p>

                    <div class="sertif-meta">
                        <span id="prev-tgl">—</span>
                        <span>Kehadiran: <strong id="prev-persen">0</strong>%</span>
                        <span>Kode Pelatihan: <strong id="prev-kode-pel">—</strong></span>
                    </div>

                    {{-- QR placeholder --}}
                    <div class="qr-box">
                        <i class="bi bi-qr-code"></i>
                    </div>

                    {{-- Footer tiga kolom --}}
                    <div class="sertif-footer">
                        <div class="sf-col">
                            <div class="sf-main" id="prev-diterbitkan">—</div>
                            <div class="sf-sub">Diterbitkan Oleh</div>
                        </div>
                        <div class="sf-col">
                            <div class="sf-kode" id="prev-kode">—</div>
                            <div class="sf-sub mt-1">Kode Sertifikat Unik</div>
                        </div>
                        <div class="sf-col">
                            <div class="sf-main" id="prev-instruktur">—</div>
                            <div class="sf-sub">Instruktur Pelaksana</div>
                        </div>
                    </div>

                    <p class="sertif-copy">
                        Verifikasi: expertindo.id/cek-sertifikat | Expertindo © 2026
                    </p>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary rounded-3 px-4"
                        data-bs-dismiss="modal">Kembali</button>

                {{-- Tombol download PDF (hanya jika file tersedia) --}}
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
    function bukaPreview(kode, nama, pelatihan, kodePel, tgl, diterbitkan, instruktur, persen, fileUrl) {
        // Isi data ke dalam modal
        document.getElementById('prev-kode').textContent        = kode;
        document.getElementById('prev-nama').textContent        = nama;
        document.getElementById('prev-pelatihan').textContent   = pelatihan;
        document.getElementById('prev-kode-pel').textContent    = kodePel;
        document.getElementById('prev-tgl').textContent         = tgl;
        document.getElementById('prev-persen').textContent      = persen;
        document.getElementById('prev-diterbitkan').textContent = diterbitkan;
        document.getElementById('prev-instruktur').textContent  = instruktur;

        // Tampilkan / sembunyikan tombol unduh PDF
        const btnDownload = document.getElementById('btn-download-pdf');
        if (fileUrl && fileUrl.trim() !== '') {
            btnDownload.href = fileUrl;
            btnDownload.style.display = 'inline-flex';
        } else {
            btnDownload.style.display = 'none';
        }

        // Buka modal
        new bootstrap.Modal(document.getElementById('modalPreview')).show();
    }
</script>
@endpush