@extends('layouts.peserta')

@section('title', 'Sertifikat Saya')
@section('page-title', 'Sertifikat Saya')

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

    /* ─── GRID SERTIFIKAT ─── */
    .sertifikat-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 20px;
    }

    /* ─── SERTIFIKAT CARD ─── */
    .sertif-card {
        background: #fff;
        border: 1px solid #eee;
        border-radius: 14px;
        overflow: hidden;
        transition: box-shadow .2s, transform .2s;
    }
    .sertif-card:hover {
        box-shadow: 0 8px 28px rgba(0,0,0,.09);
        transform: translateY(-2px);
    }

    /* Preview area — dashed border merah muda */
    .sertif-preview {
        margin: 16px 16px 0;
        border: 2px dashed #fca58a;
        border-radius: 10px;
        background: #fffaf8;
        padding: 24px 20px 20px;
        text-align: center;
        cursor: pointer;
        transition: background .15s, border-color .15s;
    }
    .sertif-preview:hover {
        background: #fff4f0;
        border-color: var(--brand);
    }

    .sertif-trophy  { font-size: 34px; margin-bottom: 10px; line-height: 1; }

    .sertif-label {
        font-size: 10px;
        font-weight: 700;
        letter-spacing: .12em;
        text-transform: uppercase;
        color: #aaa;
        margin-bottom: 8px;
    }

    .sertif-nama {
        font-size: 18px;
        font-weight: 700;
        font-style: italic;
        color: var(--brand);
        margin-bottom: 6px;
    }

    .sertif-pelatihan {
        font-size: 13px;
        color: #444;
        font-weight: 500;
        margin-bottom: 10px;
    }

    .sertif-kode {
        font-size: 10.5px;
        color: #bbb;
        font-family: monospace;
        letter-spacing: .06em;
        margin-bottom: 4px;
    }

    .sertif-terbit {
        font-size: 11.5px;
        color: #bbb;
    }

    /* Tombol bawah card */
    .sertif-actions {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 10px;
        padding: 14px 16px 16px;
    }

    .btn-preview {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        padding: 10px;
        background: #fff;
        border: 1.5px solid var(--brand);
        border-radius: 8px;
        font-family: 'Outfit', sans-serif;
        font-size: 13.5px;
        font-weight: 600;
        color: var(--brand);
        cursor: pointer;
        transition: background .15s, color .15s;
        text-decoration: none;
    }
    .btn-preview:hover { background: var(--brand-light); color: var(--brand); }

    .btn-unduh {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        padding: 10px;
        background: var(--brand);
        border: none;
        border-radius: 8px;
        font-family: 'Outfit', sans-serif;
        font-size: 13.5px;
        font-weight: 600;
        color: #fff;
        cursor: pointer;
        text-decoration: none;
        transition: background .15s;
    }
    .btn-unduh:hover { background: var(--brand-dark); color: #fff; }

    /* ─── EMPTY STATE ─── */
    .empty-state {
        text-align: center;
        padding: 64px 20px;
        color: #bbb;
    }
    .empty-state i {
        font-size: 52px;
        display: block;
        margin-bottom: 14px;
        opacity: .3;
    }
    .empty-state p { font-size: 14px; margin: 0; }

    /* ══════════════════════════════════════
       MODAL PREVIEW SERTIFIKAT
    ══════════════════════════════════════ */

    /* Wrapper sertifikat di dalam modal */
    .modal-sertif-wrap {
        border: 2px dashed #fca58a;
        border-radius: 10px;
        background: #fffaf8;
        padding: 32px 28px 28px;
        text-align: center;
        margin-bottom: 20px;
    }

    .ms-trophy  { font-size: 38px; margin-bottom: 12px; }

    .ms-title {
        font-size: 15px;
        font-weight: 800;
        letter-spacing: .12em;
        text-transform: uppercase;
        color: #1a1a2e;
        margin-bottom: 2px;
    }

    .ms-subtitle {
        font-size: 9px;
        letter-spacing: .2em;
        text-transform: uppercase;
        color: #bbb;
        margin-bottom: 18px;
    }

    .ms-diberikan {
        font-size: 11px;
        color: #aaa;
        margin-bottom: 6px;
    }

    .ms-nama {
        font-size: 24px;
        font-weight: 700;
        font-style: italic;
        color: var(--brand);
        margin-bottom: 10px;
    }

    .ms-body {
        font-size: 12px;
        color: #777;
        line-height: 1.6;
        margin-bottom: 14px;
    }

    .ms-pelatihan-nama {
        font-size: 15px;
        font-weight: 700;
        color: #1a1a2e;
        margin-bottom: 6px;
    }

    .ms-meta {
        font-size: 11px;
        color: #aaa;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 14px;
        flex-wrap: wrap;
        margin-bottom: 24px;
    }
    .ms-meta span { display: flex; align-items: center; gap: 4px; }

    /* Bagian tanda tangan */
    .ms-ttd {
        display: grid;
        grid-template-columns: 1fr auto 1fr;
        align-items: end;
        gap: 12px;
        padding-top: 16px;
        border-top: 1px dashed #f0ccc0;
        margin-top: 8px;
    }
    .ttd-col { text-align: center; }
    .ttd-name {
        font-size: 12px;
        font-weight: 700;
        color: #333;
        margin-bottom: 2px;
    }
    .ttd-role {
        font-size: 10px;
        color: #aaa;
    }
    .ttd-divider {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 2px;
        padding-bottom: 4px;
    }
    .ttd-divider i { font-size: 22px; color: #ddd; }

    /* Kode sertifikat di tengah */
    .ttd-kode-wrap { text-align: center; }
    .ttd-kode {
        font-size: 11px;
        font-family: monospace;
        color: var(--brand);
        font-weight: 700;
        letter-spacing: .04em;
    }
    .ttd-kode-label {
        font-size: 9px;
        color: #bbb;
        letter-spacing: .05em;
        text-transform: uppercase;
    }

    /* Verifikasi footer */
    .ms-verif {
        font-size: 10px;
        color: #ccc;
        margin-top: 12px;
        text-align: center;
    }
</style>
@endpush

@section('content')

{{-- ── Heading ── --}}
<div class="page-heading">
    <h5>Sertifikat Saya</h5>
</div>

{{-- ── Grid Sertifikat ── --}}
@if($sertifikat->isEmpty())

    <div class="empty-state">
        <i class="bi bi-award"></i>
        <p>Belum ada sertifikat yang diterbitkan untuk Anda.</p>
    </div>

@else

    <div class="sertifikat-grid">
        @foreach($sertifikat as $s)
            @php
                $namaPeserta  = Auth::user()->nama_lengkap ?? Auth::user()->name;
                $namaPelatihan = $s->pendaftaran?->pelatihan?->nama_pelatihan ?? '-';
                $tglTerbit     = $s->tgl_terbit
                                    ? \Carbon\Carbon::parse($s->tgl_terbit)->translatedFormat('j F Y')
                                    : '-';
            @endphp

            <div class="sertif-card">

                {{-- Preview area (klik → buka modal) --}}
                <div class="sertif-preview"
                     onclick="bukaPreview({{ $s->id_sertifikat ?? $s->id }})">
                    <div class="sertif-trophy">🏆</div>
                    <div class="sertif-label">Sertifikat Kelulusan</div>
                    <div class="sertif-nama">{{ $namaPeserta }}</div>
                    <div class="sertif-pelatihan">{{ $namaPelatihan }}</div>
                    <div class="sertif-kode">{{ $s->kode_sertifikat }}</div>
                    <div class="sertif-terbit">Terbit: {{ $tglTerbit }}</div>
                </div>

                {{-- Tombol Preview & Unduh --}}
                <div class="sertif-actions">
                    <button type="button"
                            class="btn-preview"
                            onclick="bukaPreview({{ $s->id_sertifikat ?? $s->id }})">
                        <i class="bi bi-eye"></i> Preview
                    </button>
                    <a href="{{ route('peserta.sertifikat.download', $s->id_sertifikat ?? $s->id) }}"
                       class="btn-unduh">
                        <i class="bi bi-download"></i> Unduh PDF
                    </a>
                </div>

            </div>
        @endforeach
    </div>

    {{-- ══════════════════════════════════════
         DATA SERTIFIKAT — disimpan di JS
    ══════════════════════════════════════ --}}
    <script>
        const dataSertifikat = {
            @foreach($sertifikat as $s)
            {{ $s->id_sertifikat ?? $s->id }}: {
                nama:          "{{ addslashes(Auth::user()->nama_lengkap ?? Auth::user()->name) }}",
                pelatihan:     "{{ addslashes($s->pendaftaran?->pelatihan?->nama_pelatihan ?? '-') }}",
                kode:          "{{ $s->kode_sertifikat }}",
                tglTerbit:     "{{ $s->tgl_terbit ? \Carbon\Carbon::parse($s->tgl_terbit)->translatedFormat('j F Y') : '-' }}",
                instruktur:    "{{ addslashes($s->pendaftaran?->pelatihan?->instruktur?->nama_lengkap ?? $s->pendaftaran?->pelatihan?->instruktur?->nama ?? '-') }}",
                diterbitkanOleh: "{{ addslashes($s->diterbitkan_oleh ?? 'Administrator') }}",
                kodePelatihan: "{{ $s->pendaftaran?->pelatihan?->kode_pelatihan ?? '-' }}",
                persenHadir:   "{{ $s->pendaftaran?->kualifikasiSertifikasi?->persen_hadir ?? '-' }}",
            },
            @endforeach
        };
    </script>

@endif

{{-- ══════════════════════════════════════════════════════
     MODAL PREVIEW SERTIFIKAT
══════════════════════════════════════════════════════ --}}
<div class="modal fade" id="modalPreviewSertifikat" tabindex="-1"
     aria-labelledby="modalPreviewLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
        <div class="modal-content border-0 shadow rounded-4">

            <div class="modal-header border-0 pb-0 px-4 pt-4">
                <div>
                    <h5 class="modal-title fw-bold mb-1" id="modalPreviewLabel">
                        <i class="bi bi-eye me-1" style="color:var(--brand)"></i>
                        Preview Sertifikat
                    </h5>
                    <p class="text-muted small mb-0">
                        Tampilan sertifikat yang akan di-generate. Kode unik masing-masing peserta tercantum di bagian bawah.
                    </p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body px-4 pb-0 pt-3">

                {{-- Sertifikat Preview --}}
                <div class="modal-sertif-wrap">

                    <div class="ms-trophy">🏆</div>
                    <div class="ms-title">Sertifikat Kelulusan</div>
                    <div class="ms-subtitle">Certificate of Completion</div>

                    <div class="ms-diberikan">Diberikan kepada</div>
                    <div class="ms-nama" id="prev-nama">—</div>

                    <div class="ms-body">
                        telah berhasil menyelesaikan dan dinyatakan<br>
                        lulus dalam program pelatihan
                    </div>

                    <div class="ms-pelatihan-nama" id="prev-pelatihan">—</div>

                    <div class="ms-meta">
                        <span id="prev-tgl">
                            <i class="bi bi-calendar3"></i> —
                        </span>
                        <span id="prev-hadir">
                            <i class="bi bi-person-check"></i> Kehadiran: —
                        </span>
                        <span id="prev-kode-pelatihan">
                            <i class="bi bi-upc-scan"></i> Kode Pelatihan: —
                        </span>
                    </div>

                    {{-- TTD Row --}}
                    <div class="ms-ttd">
                        <div class="ttd-col">
                            <div class="ttd-name" id="prev-diterbitkan">—</div>
                            <div class="ttd-role">Diterbitkan Oleh</div>
                        </div>

                        <div class="ttd-divider">
                            <div class="ttd-kode-wrap">
                                <i class="bi bi-qr-code" style="font-size:28px;color:#e0c0b8;margin-bottom:4px;display:block"></i>
                                <div class="ttd-kode" id="prev-kode-sertif">—</div>
                                <div class="ttd-kode-label">Kode Sertifikat Unik</div>
                            </div>
                        </div>

                        <div class="ttd-col">
                            <div class="ttd-name" id="prev-instruktur">—</div>
                            <div class="ttd-role">Instruktur Pelaksana</div>
                        </div>
                    </div>

                    <div class="ms-verif" id="prev-verif">
                        Verifikasi: expertindo.id/cek-sertifikat | Expertindo © {{ date('Y') }}
                    </div>

                </div>{{-- /modal-sertif-wrap --}}

            </div>{{-- /modal-body --}}

            <div class="modal-footer border-0 px-4 pb-4 pt-2 gap-2">
                <button type="button"
                        class="btn btn-outline-secondary rounded-3 px-4"
                        data-bs-dismiss="modal">
                    Kembali
                </button>
                <a id="btn-modal-unduh"
                   href="#"
                   class="btn btn-primary rounded-3 px-4 fw-semibold d-flex align-items-center gap-2">
                    <i class="bi bi-download"></i> Unduh PDF
                </a>
            </div>

        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    const modalEl   = document.getElementById('modalPreviewSertifikat');
    const modalBS   = new bootstrap.Modal(modalEl);

    function bukaPreview(id) {
        const d = dataSertifikat[id];
        if (!d) return;

        document.getElementById('prev-nama').textContent         = d.nama;
        document.getElementById('prev-pelatihan').textContent    = d.pelatihan;
        document.getElementById('prev-kode-sertif').textContent  = d.kode;
        document.getElementById('prev-diterbitkan').textContent  = d.diterbitkanOleh;
        document.getElementById('prev-instruktur').textContent   = d.instruktur;

        document.getElementById('prev-tgl').innerHTML =
            `<i class="bi bi-calendar3"></i> ${d.tglTerbit}`;

        document.getElementById('prev-hadir').innerHTML =
            d.persenHadir !== '-'
                ? `<i class="bi bi-person-check"></i> Kehadiran: ${d.persenHadir}%`
                : `<i class="bi bi-person-check"></i> Kehadiran: –`;

        document.getElementById('prev-kode-pelatihan').innerHTML =
            `<i class="bi bi-upc-scan"></i> Kode Pelatihan: ${d.kodePelatihan}`;

        // Tombol unduh di footer modal
        document.getElementById('btn-modal-unduh').href =
            `/peserta/sertifikat/${id}/download`;

        modalBS.show();
    }
</script>
@endpush