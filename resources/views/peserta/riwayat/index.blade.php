@extends('layouts.peserta')

@section('title', 'Riwayat Pelatihan')
@section('page-title', 'Riwayat')

@push('styles')
<style>
    /* ─── PAGE HEADING ─── */
    .page-heading { margin-bottom: 24px; }
    .page-heading h5 { font-size: 17px; font-weight: 700; color: #1a1a2e; margin: 0; }

    /* ─── PANEL ─── */
    .panel { background: #fff; border: 1px solid #eee; border-radius: 14px; overflow: hidden; }
    .panel-header {
        padding: 14px 20px;
        border-bottom: 1px solid #f0f0f0;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 10px;
    }
    .panel-header h6 { margin: 0; font-size: 14px; font-weight: 600; color: #333; }

    /* ─── TABLE ─── */
    .table-custom { width: 100%; font-size: 13px; border-collapse: collapse; margin: 0; }
    .table-custom thead th {
        padding: 10px 16px;
        text-align: left;
        font-size: 11px;
        font-weight: 700;
        color: #888;
        text-transform: uppercase;
        letter-spacing: .05em;
        background: #f9fafb;
        border-bottom: 1px solid #f0f0f0;
        white-space: nowrap;
    }
    .table-custom tbody tr { border-bottom: 1px solid #f8f8f8; transition: background .12s; }
    .table-custom tbody tr:last-child { border-bottom: none; }
    .table-custom tbody tr:hover { background: #fafafa; }
    .table-custom tbody td { padding: 13px 16px; color: #444; vertical-align: middle; }

    .id-badge {
        font-family: monospace; font-size: 11.5px;
        background: #f3f4f6; color: #555;
        padding: 3px 9px; border-radius: 5px; font-weight: 600;
    }
    .pelatihan-nama { font-weight: 600; color: #222; font-size: 13.5px; }
    .pelatihan-kode { font-size: 11px; color: #aaa; font-family: monospace; margin-top: 2px; }
    .tgl-text { font-size: 13px; color: #666; white-space: nowrap; }

    .badge-diterima { background:#dcfce7;color:#15803d;padding:3px 10px;border-radius:999px;font-size:11px;font-weight:600; }
    .badge-menunggu { background:#fef9c3;color:#854d0e;padding:3px 10px;border-radius:999px;font-size:11px;font-weight:600; }
    .badge-ditolak  { background:#fee2e2;color:#991b1b;padding:3px 10px;border-radius:999px;font-size:11px;font-weight:600; }
    .badge-tersedia { background:#dcfce7;color:#15803d;padding:3px 10px;border-radius:999px;font-size:11px;font-weight:600; }
    .badge-selesai  { background:#f3f4f6;color:#374151;padding:3px 10px;border-radius:999px;font-size:11px;font-weight:600; }
    .badge-penuh    { background:#fee2e2;color:#991b1b;padding:3px 10px;border-radius:999px;font-size:11px;font-weight:600; }

    .kode-sertif {
        font-family: monospace; font-size: 11px;
        background: #f3f4f6; color: #555;
        padding: 3px 8px; border-radius: 4px;
    }
    .no-data { font-size: 12px; color: #d1d5db; font-style: italic; }

    /* Tombol aksi di tabel */
    .btn-aksi-pdf {
        display: inline-flex; align-items: center; gap: 5px;
        padding: 6px 13px;
        background: #fff;
        border: 1.5px solid var(--brand);
        border-radius: 7px;
        font-family: 'Outfit', sans-serif;
        font-size: 12.5px; font-weight: 600;
        color: var(--brand); cursor: pointer;
        text-decoration: none;
        transition: background .15s, color .15s;
        white-space: nowrap;
    }
    .btn-aksi-pdf:hover { background: var(--brand); color: #fff; }

    /* ─── EMPTY STATE ─── */
    .empty-state { padding: 52px 20px; text-align: center; color: #bbb; font-size: 13.5px; }
    .empty-state i { font-size: 40px; display: block; margin-bottom: 10px; opacity: .35; }
    .empty-state a {
        display: inline-flex; align-items: center; gap: 6px;
        margin-top: 14px; padding: 9px 20px;
        background: var(--brand); color: #fff; border-radius: 8px;
        font-size: 13.5px; font-weight: 600; text-decoration: none;
        transition: background .15s;
    }
    .empty-state a:hover { background: var(--brand-dark); color: #fff; }

    /* ─── PAGINATION ─── */
    .paginasi { padding: 14px 20px; border-top: 1px solid #f0f0f0; }

    /* ══════════════════════════════════════
       MODAL FORMULIR PENDAFTARAN
    ══════════════════════════════════════ */

    /* Header modal merah */
    .modal-form-header {
        background: var(--brand);
        padding: 16px 20px;
        border-radius: 0;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
    }
    .mfh-left { display: flex; align-items: center; gap: 12px; }
    .mfh-icon {
        width: 38px; height: 38px;
        background: rgba(255,255,255,.2);
        border-radius: 8px;
        display: flex; align-items: center; justify-content: center;
        font-size: 18px; color: #fff; flex-shrink: 0;
    }
    .mfh-text {}
    .mfh-title {
        font-size: 13px; font-weight: 700;
        color: #fff; letter-spacing: .04em;
        text-transform: uppercase; line-height: 1.2;
    }
    .mfh-sub { font-size: 10px; color: rgba(255,255,255,.65); margin-top: 2px; }
    .mfh-right { text-align: right; flex-shrink: 0; }
    .mfh-no-label { font-size: 9px; color: rgba(255,255,255,.6); text-transform: uppercase; letter-spacing: .06em; }
    .mfh-no-value {
        font-size: 13px; font-weight: 700; color: #fff;
        font-family: monospace; letter-spacing: .04em;
    }

    /* Info pelatihan box */
    .info-pelatihan-box {
        background: #fff0ee;
        border: 1px solid #fcd0c4;
        border-radius: 8px;
        padding: 14px 16px;
        margin-bottom: 16px;
    }
    .ipb-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 8px 16px;
    }
    .ipb-item .ipb-label { font-size: 10px; color: #aaa; font-weight: 600; text-transform: uppercase; letter-spacing: .04em; margin-bottom: 2px; }
    .ipb-item .ipb-value { font-size: 13px; font-weight: 600; color: #1a1a2e; }

    /* Section label */
    .form-section-lbl {
        display: flex; align-items: center; gap: 6px;
        font-size: 11px; font-weight: 700;
        color: var(--brand);
        text-transform: uppercase; letter-spacing: .06em;
        margin: 16px 0 10px;
    }
    .form-section-lbl i { font-size: 13px; }
    .form-section-lbl::after {
        content: '';
        flex: 1;
        height: 1px;
        background: #f0f0f0;
        margin-left: 6px;
    }

    /* Data grid di modal */
    .data-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 10px 16px;
        margin-bottom: 4px;
    }
    .data-grid.single { grid-template-columns: 1fr; }
    .data-item .di-label { font-size: 10.5px; color: #aaa; margin-bottom: 3px; }
    .data-item .di-value {
        font-size: 13px; color: #333;
        background: #f9fafb;
        border: 1px solid #f0f0f0;
        border-radius: 6px;
        padding: 7px 11px;
        min-height: 34px;
    }

    /* Status pendaftaran di modal */
    .modal-status-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-top: 14px;
        padding-top: 14px;
        border-top: 1px solid #f0f0f0;
    }
    .modal-status-label { font-size: 12px; color: #888; font-weight: 600; }
    .modal-ttd {
        font-size: 11.5px; color: #aaa;
        border: 1px dashed #ddd;
        border-radius: 6px;
        padding: 6px 16px;
        font-style: italic;
    }

    /* Footer kecil di modal */
    .modal-doc-footer {
        text-align: center;
        font-size: 10px;
        color: #ccc;
        margin-top: 10px;
        padding-top: 10px;
        border-top: 1px dashed #eee;
    }

    /* Spinner loading */
    .modal-loading {
        text-align: center;
        padding: 40px 20px;
        color: #bbb;
    }
    .modal-loading .spinner-border { color: var(--brand); }
</style>
@endpush

@section('content')

{{-- ── Heading ── --}}
<div class="page-heading">
    <h5>Riwayat Pelatihan</h5>
</div>

{{-- ── Panel Tabel ── --}}
<div class="panel">

    <div class="panel-header">
        <h6>Daftar Riwayat Pendaftaran</h6>
        <span style="font-size:12px;color:#aaa">Total: {{ $pendaftaran->total() }} pendaftaran</span>
    </div>

    <div class="table-responsive">
        <table class="table-custom">
            <thead>
                <tr>
                    <th>ID Pendaftaran</th>
                    <th>Pelatihan</th>
                    <th>Tanggal Daftar</th>
                    <th>Status Pendaftaran</th>
                    <th>Status Pelatihan</th>
                    <th>Sertifikat</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pendaftaran as $p)
                <tr>
                    <td>
                        <span class="id-badge">
                            PD-{{ str_pad($p->id_pendaftaran ?? $p->id, 3, '0', STR_PAD_LEFT) }}
                        </span>
                    </td>
                    <td>
                        <div class="pelatihan-nama">{{ $p->pelatihan?->nama_pelatihan ?? '-' }}</div>
                        <div class="pelatihan-kode">{{ $p->pelatihan?->kode_pelatihan ?? '' }}</div>
                    </td>
                    <td>
                        <span class="tgl-text">
                            {{ $p->tgl_daftar ? \Carbon\Carbon::parse($p->tgl_daftar)->translatedFormat('j M Y') : '-' }}
                        </span>
                    </td>
                    <td><span class="badge-{{ $p->status }}">{{ ucfirst($p->status) }}</span></td>
                    <td>
                        @if($p->pelatihan)
                            <span class="badge-{{ $p->pelatihan->status }}">{{ ucfirst($p->pelatihan->status) }}</span>
                        @else
                            <span class="no-data">–</span>
                        @endif
                    </td>
                    <td>
                        @if($p->sertifikat)
                            <span class="kode-sertif">{{ $p->sertifikat->kode_sertifikat }}</span>
                        @else
                            <span class="no-data">Belum terbit</span>
                        @endif
                    </td>
                    <td>
                        {{-- Klik → buka modal formulir --}}
                        <button type="button"
                                class="btn-aksi-pdf"
                                onclick="bukaFormulir({{ $p->id_pendaftaran ?? $p->id }})">
                            <i class="bi bi-file-earmark-pdf"></i> Unduh PDF
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7">
                        <div class="empty-state">
                            <i class="bi bi-clock-history"></i>
                            <p>Belum ada riwayat pendaftaran.</p>
                            <a href="{{ route('peserta.pelatihan.index') }}">
                                <i class="bi bi-collection"></i> Lihat Katalog Pelatihan
                            </a>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($pendaftaran->hasPages())
        <div class="paginasi d-flex justify-content-center">
            {{ $pendaftaran->links('pagination::bootstrap-5') }}
        </div>
    @endif

</div>

{{-- ══════════════════════════════════════════════════════
     MODAL FORMULIR PENDAFTARAN
══════════════════════════════════════════════════════ --}}
<div class="modal fade" id="modalFormulir" tabindex="-1"
     aria-labelledby="modalFormulirLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow rounded-4 overflow-hidden">

            {{-- Header merah --}}
            <div class="modal-form-header">
                <div class="mfh-left">
                    <div class="mfh-icon"><i class="bi bi-file-earmark-text-fill"></i></div>
                    <div class="mfh-text">
                        <div class="mfh-title">Formulir Pendaftaran Pelatihan</div>
                        <div class="mfh-sub">Sistem Informasi Manajemen Pelatihan &amp; Sertifikasi</div>
                    </div>
                </div>
                <div class="mfh-right">
                    <div class="mfh-no-label">No. Pendaftaran</div>
                    <div class="mfh-no-value" id="modal-no-pendaftaran">—</div>
                </div>
            </div>

            {{-- Tombol tutup pojok kanan atas --}}
            <button type="button"
                    class="btn-close position-absolute"
                    style="top:12px;right:12px;filter:invert(1);opacity:.7;z-index:10"
                    data-bs-dismiss="modal"></button>

            {{-- Body --}}
            <div class="modal-body px-4 pb-2 pt-3" id="modalFormulirBody">

                {{-- Loading state --}}
                <div class="modal-loading" id="modalLoading">
                    <div class="spinner-border" role="status" style="width:2rem;height:2rem">
                        <span class="visually-hidden">Loading…</span>
                    </div>
                    <div style="margin-top:10px;font-size:13px">Memuat data…</div>
                </div>

                {{-- Konten (tersembunyi saat loading) --}}
                <div id="modalKonten" style="display:none">

                    {{-- Info Pelatihan --}}
                    <div class="form-section-lbl">
                        <i class="bi bi-journal-bookmark-fill"></i> Informasi Pelatihan
                    </div>
                    <div class="info-pelatihan-box">
                        <div class="ipb-grid">
                            <div class="ipb-item">
                                <div class="ipb-label">Nama Pelatihan</div>
                                <div class="ipb-value" id="m-nama-pelatihan">—</div>
                            </div>
                            <div class="ipb-item">
                                <div class="ipb-label">Kode</div>
                                <div class="ipb-value" id="m-kode-pelatihan">—</div>
                            </div>
                            <div class="ipb-item">
                                <div class="ipb-label">Instruktur</div>
                                <div class="ipb-value" id="m-instruktur">—</div>
                            </div>
                            <div class="ipb-item">
                                <div class="ipb-label">Periode</div>
                                <div class="ipb-value" id="m-periode">—</div>
                            </div>
                        </div>
                    </div>

                    {{-- Data Diri --}}
                    <div class="form-section-lbl">
                        <i class="bi bi-person-lines-fill"></i> Data Diri Peserta
                    </div>
                    <div class="data-grid">
                        <div class="data-item">
                            <div class="di-label">Nama Lengkap</div>
                            <div class="di-value" id="m-nama-lengkap">—</div>
                        </div>
                        <div class="data-item">
                            <div class="di-label">Email</div>
                            <div class="di-value" id="m-email">—</div>
                        </div>
                        <div class="data-item">
                            <div class="di-label">No. HP</div>
                            <div class="di-value" id="m-no-hp">—</div>
                        </div>
                        <div class="data-item">
                            <div class="di-label">Tanggal Daftar</div>
                            <div class="di-value" id="m-tgl-daftar">—</div>
                        </div>
                    </div>
                    <div class="data-grid single" style="margin-top:10px">
                        <div class="data-item">
                            <div class="di-label">Alamat</div>
                            <div class="di-value" id="m-alamat" style="min-height:48px">—</div>
                        </div>
                    </div>

                    {{-- Data Pekerjaan --}}
                    <div class="form-section-lbl" style="margin-top:14px">
                        <i class="bi bi-briefcase-fill"></i> Data Pekerjaan &amp; Perusahaan
                    </div>
                    <div class="data-grid">
                        <div class="data-item">
                            <div class="di-label">Pekerjaan / Jabatan</div>
                            <div class="di-value" id="m-pekerjaan">—</div>
                        </div>
                        <div class="data-item">
                            <div class="di-label">Nama Perusahaan</div>
                            <div class="di-value" id="m-perusahaan">—</div>
                        </div>
                    </div>
                    <div class="data-grid single" style="margin-top:10px">
                        <div class="data-item">
                            <div class="di-label">No. Telp Perusahaan</div>
                            <div class="di-value" id="m-tlp-perusahaan">—</div>
                        </div>
                    </div>

                    {{-- Pesan --}}
                    <div class="form-section-lbl" style="margin-top:14px">
                        <i class="bi bi-chat-left-text-fill"></i> Pesan / Keterangan
                    </div>
                    <div class="data-grid single">
                        <div class="data-item">
                            <div class="di-value" id="m-pesan" style="min-height:54px">—</div>
                        </div>
                    </div>

                    {{-- Status & TTD --}}
                    <div class="modal-status-row">
                        <div>
                            <div class="modal-status-label" style="margin-bottom:5px">Status Pendaftaran</div>
                            <span id="m-status-badge">—</span>
                        </div>
                        <div class="modal-ttd">Tanda Tangan Peserta</div>
                    </div>

                    {{-- Footer dokumen --}}
                    <div class="modal-doc-footer">
                        Sistem Informasi Manajemen Pelatihan &amp; Sertifikasi
                        &nbsp;|&nbsp; Dicetak: {{ now()->translatedFormat('j M Y') }}
                    </div>

                </div>{{-- /modalKonten --}}
            </div>{{-- /modal-body --}}

            {{-- Footer modal --}}
            <div class="modal-footer border-0 px-4 pb-4 pt-2 gap-2">
                <button type="button"
                        class="btn btn-outline-secondary rounded-3 px-4"
                        data-bs-dismiss="modal">
                    Tutup
                </button>
                <a id="btn-unduh-pdf-modal"
                   href="#"
                   class="btn btn-primary rounded-3 px-4 fw-semibold d-flex align-items-center gap-2">
                    <i class="bi bi-download"></i> Unduh sebagai PDF
                </a>
            </div>

        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    const modalEl = document.getElementById('modalFormulir');
    const modalBS = new bootstrap.Modal(modalEl);

    // Badge status HTML
    const badgeMap = {
        diterima: '<span class="badge-diterima">Diterima</span>',
        menunggu: '<span class="badge-menunggu">Menunggu</span>',
        ditolak:  '<span class="badge-ditolak">Ditolak</span>',
    };

    function set(id, val) {
        const el = document.getElementById(id);
        if (el) el.textContent = val || '-';
    }

    function bukaFormulir(idPendaftaran) {
        // Reset & tampilkan loading
        document.getElementById('modalLoading').style.display  = 'block';
        document.getElementById('modalKonten').style.display   = 'none';
        document.getElementById('modal-no-pendaftaran').textContent = '—';
        document.getElementById('btn-unduh-pdf-modal').href    = '#';
        modalBS.show();

        // Fetch JSON via AJAX
        fetch(`/peserta/riwayat/${idPendaftaran}/detail`, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            }
        })
        .then(res => {
            if (!res.ok) throw new Error('Gagal memuat data.');
            return res.json();
        })
        .then(d => {
            // Isi semua field
            document.getElementById('modal-no-pendaftaran').textContent = d.id_label;
            set('m-nama-pelatihan', d.pelatihan.nama);
            set('m-kode-pelatihan', d.pelatihan.kode);
            set('m-instruktur',     d.pelatihan.instruktur);
            set('m-periode',        d.pelatihan.periode);
            set('m-nama-lengkap',   d.nama_lengkap);
            set('m-email',          d.email);
            set('m-no-hp',          d.no_hp);
            set('m-tgl-daftar',     d.tgl_daftar);
            set('m-alamat',         d.alamat);
            set('m-pekerjaan',      d.pekerjaan);
            set('m-perusahaan',     d.perusahaan);
            set('m-tlp-perusahaan', d.tlp_perusahaan);
            set('m-pesan',          d.pesan);

            // Badge status
            document.getElementById('m-status-badge').innerHTML =
                badgeMap[d.status] ?? `<span>${d.status}</span>`;

            // Tombol unduh PDF
            document.getElementById('btn-unduh-pdf-modal').href = d.url_pdf;

            // Tampilkan konten, sembunyikan loading
            document.getElementById('modalLoading').style.display = 'none';
            document.getElementById('modalKonten').style.display  = 'block';
        })
        .catch(err => {
            document.getElementById('modalLoading').innerHTML =
                `<div class="text-danger"><i class="bi bi-exclamation-circle me-1"></i>${err.message}</div>`;
        });
    }
</script>
@endpush