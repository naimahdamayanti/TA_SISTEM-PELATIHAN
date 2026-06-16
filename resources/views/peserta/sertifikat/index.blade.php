@extends('layouts.peserta')

@section('title', 'Sertifikat Saya')
@section('page-title', 'Sertifikat Saya')

@push('styles')
<style>
    .page-heading { margin-bottom: 24px; }
    .page-heading h5 { font-size: 17px; font-weight: 700; color: #1a1a2e; margin: 0; }

    .sertifikat-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 20px;
    }

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
    .sertif-preview:hover { background: #fff4f0; border-color: var(--brand); }

    .sertif-trophy  { font-size: 34px; margin-bottom: 10px; line-height: 1; }
    .sertif-label   { font-size: 10px; font-weight: 700; letter-spacing: .12em; text-transform: uppercase; color: #aaa; margin-bottom: 8px; }
    .sertif-nama    { font-size: 18px; font-weight: 700; font-style: italic; color: var(--brand); margin-bottom: 6px; }
    .sertif-pelatihan { font-size: 13px; color: #444; font-weight: 500; margin-bottom: 10px; }
    .sertif-nomor   { font-size: 10.5px; color: #aaa; font-family: monospace; letter-spacing: .04em; margin-bottom: 2px; }
    .sertif-kode    { font-size: 10px; color: #ccc; font-family: monospace; letter-spacing: .06em; margin-bottom: 4px; }
    .sertif-terbit  { font-size: 11.5px; color: #bbb; }

    .sertif-actions {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 10px;
        padding: 14px 16px 16px;
    }

    .btn-preview {
        display: flex; align-items: center; justify-content: center; gap: 6px;
        padding: 10px; background: #fff; border: 1.5px solid var(--brand);
        border-radius: 8px; font-family: 'Outfit', sans-serif; font-size: 13.5px;
        font-weight: 600; color: var(--brand); cursor: pointer;
        transition: background .15s, color .15s; text-decoration: none;
    }
    .btn-preview:hover { background: var(--brand-light); color: var(--brand); }

    .btn-unduh {
        display: flex; align-items: center; justify-content: center; gap: 6px;
        padding: 10px; background: var(--brand); border: none; border-radius: 8px;
        font-family: 'Outfit', sans-serif; font-size: 13.5px; font-weight: 600;
        color: #fff; cursor: pointer; text-decoration: none; transition: background .15s;
    }
    .btn-unduh:hover { background: var(--brand-dark); color: #fff; }

    .empty-state { text-align: center; padding: 64px 20px; color: #bbb; }
    .empty-state i { font-size: 52px; display: block; margin-bottom: 14px; opacity: .3; }
    .empty-state p { font-size: 14px; margin: 0; }

    /* ── Modal ── */
    .modal-sertif-wrap {
        border: 2px dashed #fca58a;
        border-radius: 10px;
        background: #fffaf8;
        padding: 32px 28px 28px;
        text-align: center;
        margin-bottom: 20px;
    }

    .ms-trophy    { font-size: 38px; margin-bottom: 12px; }
    .ms-title     { font-size: 15px; font-weight: 800; letter-spacing: .12em; text-transform: uppercase; color: #1a1a2e; margin-bottom: 2px; }
    .ms-subtitle  { font-size: 9px; letter-spacing: .2em; text-transform: uppercase; color: #bbb; margin-bottom: 18px; }
    .ms-diberikan { font-size: 11px; color: #aaa; margin-bottom: 6px; }
    .ms-nama      { font-size: 24px; font-weight: 700; font-style: italic; color: var(--brand); margin-bottom: 10px; }
    .ms-body      { font-size: 12px; color: #777; line-height: 1.6; margin-bottom: 14px; }
    .ms-pelatihan-nama { font-size: 15px; font-weight: 700; color: #1a1a2e; margin-bottom: 6px; }

    .ms-meta {
        font-size: 11px; color: #aaa;
        display: flex; align-items: center; justify-content: center;
        gap: 14px; flex-wrap: wrap; margin-bottom: 24px;
    }
    .ms-meta span { display: flex; align-items: center; gap: 4px; }

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
    .ttd-col img {
        max-height: 48px;
        max-width: 120px;
        object-fit: contain;
        margin-bottom: 6px;
        display: block;
        margin-left: auto;
        margin-right: auto;
    }
    .ttd-name  { font-size: 12px; font-weight: 700; color: #333; margin-bottom: 2px; }
    .ttd-role  { font-size: 10px; color: #aaa; }

    .ttd-divider {
        display: flex; flex-direction: column;
        align-items: center; gap: 2px; padding-bottom: 4px;
    }
    .ttd-divider i { font-size: 22px; color: #ddd; }

    .ttd-kode-wrap  { text-align: center; }
    .ttd-kode       { font-size: 11px; font-family: monospace; color: var(--brand); font-weight: 700; letter-spacing: .04em; }
    .ttd-kode-label { font-size: 9px; color: #bbb; letter-spacing: .05em; text-transform: uppercase; }

    .ms-verif { font-size: 10px; color: #ccc; margin-top: 12px; text-align: center; }
</style>
@endpush

@section('content')

<div class="page-heading">
    <h5>Sertifikat Saya</h5>
</div>

@if($sertifikat->isEmpty())

    <div class="empty-state">
        <i class="bi bi-award"></i>
        <p>Belum ada sertifikat yang diterbitkan untuk Anda.</p>
    </div>

@else

    <div class="sertifikat-grid">
        @foreach($sertifikat as $s)
            @php
                $namaPeserta   = Auth::user()->nama_lengkap ?? Auth::user()->name;
                $namaPelatihan = $s->pendaftaran?->pelatihan?->nama_pelatihan ?? '-';
                $tglTerbit     = $s->tgl_terbit
                    ? \Carbon\Carbon::parse($s->tgl_terbit)->translatedFormat('j F Y')
                    : '-';
                $ttdUrl = $s->pendaftaran?->pelatihan?->tanda_tangan
                    ? \Illuminate\Support\Facades\Storage::url($s->pendaftaran->pelatihan->tanda_tangan)
                    : null;
                $fileUrl = $s->file                                          
                    ? \Illuminate\Support\Facades\Storage::url($s->file)
                    : null;
                $templateUrl = $s->pendaftaran?->pelatihan?->template_sertifikat   
                    ? \Illuminate\Support\Facades\Storage::url($s->pendaftaran->pelatihan->template_sertifikat)
                    : null;
            @endphp

            <div class="sertif-card">

                <div class="sertif-preview"
                    onclick="bukaPreview({{ $s->id_sertifikat ?? $s->id }})">

                    @if($templateUrl)
                        {{-- Tampilkan gambar template sebagai miniatur sertifikat --}}
                        <div style="position:relative; width:100%; aspect-ratio:297/210;
                                    border-radius:8px; overflow:hidden; margin-bottom:10px;">
                            <img src="{{ $templateUrl }}"
                                style="width:100%;height:100%;object-fit:fill;display:block;">
                            {{-- Overlay gelap tipis supaya teks terbaca --}}
                            <div style="position:absolute;inset:0;background:rgba(0,0,0,0.18);"></div>
                            {{-- Info overlay di atas gambar --}}
                            <div style="position:absolute;inset:0;display:flex;flex-direction:column;
                                        align-items:center;justify-content:center;gap:2px;padding:8px;">
                                <div style="font-size:11px;font-weight:700;color:#fff;
                                            text-shadow:0 1px 4px rgba(0,0,0,0.6);text-align:center;">
                                    {{ $namaPeserta }}
                                </div>
                                <div style="font-size:9px;color:rgba(255,255,255,0.85);
                                            text-shadow:0 1px 3px rgba(0,0,0,0.5);text-align:center;">
                                    {{ $namaPelatihan }}
                                </div>
                            </div>
                        </div>
                        {{-- Info di bawah gambar --}}
                        <div class="sertif-nomor">{{ $s->nomor_sertifikat ?? '-' }}</div>
                        <div class="sertif-kode">{{ $s->kode_sertifikat }}</div>
                        <div class="sertif-terbit">Terbit: {{ $tglTerbit }}</div>

                    @else
                        {{-- Fallback kalau template belum diupload --}}
                        <div class="sertif-trophy">🏆</div>
                        <div class="sertif-label">Sertifikat Kelulusan</div>
                        <div class="sertif-nama">{{ $namaPeserta }}</div>
                        <div class="sertif-pelatihan">{{ $namaPelatihan }}</div>
                        <div class="sertif-nomor">{{ $s->nomor_sertifikat ?? '-' }}</div>
                        <div class="sertif-kode">{{ $s->kode_sertifikat }}</div>
                        <div class="sertif-terbit">Terbit: {{ $tglTerbit }}</div>
                    @endif

                </div>

                <div class="sertif-actions">
                    <button type="button" class="btn-preview"
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

    <script>
        const dataSertifikat = {
            @foreach($sertifikat as $s)
            {{ $s->id_sertifikat ?? $s->id }}: {
                nama:            "{{ addslashes(Auth::user()->nama_lengkap ?? Auth::user()->name) }}",
                pelatihan:       "{{ addslashes($s->pendaftaran?->pelatihan?->nama_pelatihan ?? '-') }}",
                kode:            "{{ $s->kode_sertifikat }}",
                nomorSertifikat: "{{ $s->nomor_sertifikat ?? '-' }}",
                tglTerbit:       "{{ $s->tgl_terbit ? \Carbon\Carbon::parse($s->tgl_terbit)->translatedFormat('j F Y') : '-' }}",
                instruktur:      "{{ addslashes($s->pendaftaran?->pelatihan?->instruktur?->nama_lengkap ?? $s->pendaftaran?->pelatihan?->instruktur?->nama ?? '-') }}",
                diterbitkanOleh: "{{ addslashes($s->diterbitkan_oleh ?? 'Administrator') }}",
                kodePelatihan:   "{{ $s->pendaftaran?->pelatihan?->kode_pelatihan ?? '-' }}",
                ttdUrl:          "{{ $ttdUrl ?? '' }}",
                fileUrl:         "{{ $fileUrl ?? '' }}",
            },
            @endforeach
        };
    </script>

@endif

{{-- ── Modal Preview ── --}}
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
                        Tampilan ringkasan sertifikat Anda. Unduh PDF untuk dokumen resmi.
                    </p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body px-4 pb-0 pt-3">
                <div class="modal-body px-3 pb-0 pt-3">

                    {{-- Iframe PDF --}}
                    <div id="prev-pdf-wrap"
                        style="border:1px solid #dee2e6; border-radius:10px; overflow:hidden;
                                background:#f5f5f5; height:460px; display:none;">
                        <iframe id="prev-pdf-iframe" src="" style="width:100%;height:100%;border:0;"></iframe>
                    </div>

                    {{-- Fallback kalau PDF belum ada --}}
                    <div id="prev-pdf-fallback"
                        style="height:200px;display:flex;flex-direction:column;
                                align-items:center;justify-content:center;
                                border:2px dashed #fca58a;border-radius:10px;
                                background:#fffaf8;color:#bbb;">
                        <i class="bi bi-file-earmark-pdf" style="font-size:40px;margin-bottom:8px;"></i>
                        <div style="font-size:13px;">PDF sertifikat belum tersedia</div>
                    </div>

                    {{-- Info ringkas di bawah PDF --}}
                    <div id="prev-info-strip"
                        style="display:none; margin-top:12px; padding:10px 14px;
                                background:#fffaf8; border:1px solid #fde8dd;
                                border-radius:8px; font-size:12px; color:#666;">
                        <div class="d-flex justify-content-between flex-wrap gap-1">
                            <span><i class="bi bi-hash me-1 text-muted"></i><span id="prev-nomor-sertifikat-strip">—</span></span>
                            <span><i class="bi bi-upc-scan me-1 text-muted"></i><span id="prev-kode-strip">—</span></span>
                            <span><i class="bi bi-calendar3 me-1 text-muted"></i><span id="prev-tgl-strip">—</span></span>
                        </div>
                    </div>

                </div>
            </div>

            <div class="modal-footer border-0 px-4 pb-4 pt-2 gap-2">
                <button type="button"
                        class="btn btn-outline-secondary rounded-3 px-4"
                        data-bs-dismiss="modal">
                    Kembali
                </button>
                <a id="btn-modal-unduh" href="#"
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
    const modalEl = document.getElementById('modalPreviewSertifikat');
    const modalBS = new bootstrap.Modal(modalEl);

    function bukaPreview(id) {
    const d = dataSertifikat[id];
    if (!d) return;

    const iframe    = document.getElementById('prev-pdf-iframe');
    const pdfWrap   = document.getElementById('prev-pdf-wrap');
    const fallback  = document.getElementById('prev-pdf-fallback');
    const infoStrip = document.getElementById('prev-info-strip');

    if (d.fileUrl) {
        iframe.src              = d.fileUrl;
        pdfWrap.style.display   = 'block';
        fallback.style.display  = 'none';
        infoStrip.style.display = 'block';
    } else {
        iframe.src              = '';
        pdfWrap.style.display   = 'none';
        fallback.style.display  = 'flex';
        infoStrip.style.display = 'none';
    }

    document.getElementById('prev-nomor-sertifikat-strip').textContent = d.nomorSertifikat;
    document.getElementById('prev-kode-strip').textContent             = d.kode;
    document.getElementById('prev-tgl-strip').textContent              = d.tglTerbit;

    document.getElementById('btn-modal-unduh').href = `/peserta/sertifikat/${id}/download`;

    modalBS.show();
}
</script>
@endpush