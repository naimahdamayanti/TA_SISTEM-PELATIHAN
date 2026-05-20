@extends('layouts.admin')

@section('title', 'Sertifikat')

@section('content')

<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="fw-bold mb-1">Manajemen Sertifikat</h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 small">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Sertifikat</li>
            </ol>
        </nav>
    </div>
    <button type="button" class="btn btn-primary px-4 fw-semibold" onclick="openModalTerbitkan()">
        <i class="bi bi-patch-check me-2"></i>Terbitkan Sertifikat
    </button>
</div>

{{-- Alert --}}
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show rounded-3 mb-4" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif
@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show rounded-3 mb-4" role="alert">
        <i class="bi bi-exclamation-circle-fill me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

{{-- Stat Cards --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-xl-3">
        <div class="card border-0 shadow-sm rounded-3 h-100">
            <div class="card-body d-flex align-items-center gap-3 py-3 px-4">
                <div class="rounded-3 d-flex align-items-center justify-content-center text-white"
                     style="width:48px;height:48px;background:linear-gradient(135deg,#e84e3a,#c0392b);flex-shrink:0">
                    <i class="bi bi-patch-check-fill fs-5"></i>
                </div>
                <div>
                    <div class="fw-bold lh-1 mb-1" style="font-size:1.7rem">{{ $sertifikat->total() }}</div>
                    <div class="fw-semibold small">Total Diterbitkan</div>
                    <div class="text-muted" style="font-size:11px">Semua waktu</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="card border-0 shadow-sm rounded-3 h-100">
            <div class="card-body d-flex align-items-center gap-3 py-3 px-4">
                <div class="rounded-3 d-flex align-items-center justify-content-center text-white"
                     style="width:48px;height:48px;background:linear-gradient(135deg,#f1c40f,#f39c12);flex-shrink:0">
                    <i class="bi bi-hourglass-split fs-5"></i>
                </div>
                <div>
                    <div class="fw-bold lh-1 mb-1" style="font-size:1.7rem">{{ $menungguSertifikat }}</div>
                    <div class="fw-semibold small">Menunggu Terbit</div>
                    <div class="text-muted" style="font-size:11px">Layak belum terbit</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="card border-0 shadow-sm rounded-3 h-100">
            <div class="card-body d-flex align-items-center gap-3 py-3 px-4">
                <div class="rounded-3 d-flex align-items-center justify-content-center text-white"
                     style="width:48px;height:48px;background:linear-gradient(135deg,#e74c3c,#c0392b);flex-shrink:0">
                    <i class="bi bi-x-circle-fill fs-5"></i>
                </div>
                <div>
                    <div class="fw-bold lh-1 mb-1" style="font-size:1.7rem">
                        {{ \App\Models\KualifikasiSertifikasiModel::where('memenuhi_syarat', false)->count() }}
                    </div>
                    <div class="fw-semibold small">Tidak Memenuhi</div>
                    <div class="text-muted" style="font-size:11px">Kehadiran &lt; 80%</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="card border-0 shadow-sm rounded-3 h-100">
            <div class="card-body d-flex align-items-center gap-3 py-3 px-4">
                <div class="rounded-3 d-flex align-items-center justify-content-center text-white"
                     style="width:48px;height:48px;background:linear-gradient(135deg,#2ecc71,#27ae60);flex-shrink:0">
                    <i class="bi bi-download fs-5"></i>
                </div>
                <div>
                    <div class="fw-bold lh-1 mb-1" style="font-size:1.7rem">
                        {{ \App\Models\SertifikatModel::whereNotNull('file')->count() }}
                    </div>
                    <div class="fw-semibold small">Sudah Diunduh</div>
                    <div class="text-muted" style="font-size:11px">Oleh peserta</div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Filter + Tabel --}}
<div class="card border-0 shadow-sm rounded-3">

    {{-- Filter --}}
    <div class="card-body border-bottom py-3 px-4">
        <form method="GET" action="{{ route('admin.sertifikat.index') }}" class="row g-2 align-items-center">
            <div class="col-12 col-md-5">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0">
                        <i class="bi bi-search text-muted"></i>
                    </span>
                    <input type="text" name="search" value="{{ request('search') }}"
                        class="form-control border-start-0 ps-0"
                        placeholder="Cari kode / nama sertifikat...">
                </div>
            </div>
            <div class="col-12 col-md-4">
                <select name="pelatihan_id" class="form-select">
                    <option value="">Semua Pelatihan</option>
                    @foreach($pelatihan as $p)
                        <option value="{{ $p->id_pelatihan }}"
                            @selected(request('pelatihan_id') == $p->id_pelatihan)>
                            {{ $p->nama_pelatihan }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-auto d-flex gap-2">
                <button type="submit" class="btn btn-primary px-4">Filter</button>
                <a href="{{ route('admin.sertifikat.index') }}" class="btn btn-outline-secondary" title="Reset">
                    <i class="bi bi-arrow-counterclockwise"></i>
                </a>
            </div>
        </form>
    </div>

    {{-- Tabel --}}
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4 py-3">Kode Sertifikat <span class="text-muted fw-normal">(unik)</span></th>
                        <th class="py-3">Nama Peserta</th>
                        <th class="py-3">Pelatihan</th>
                        <th class="py-3">Tanggal Terbit</th>
                        <th class="py-3">Diterbitkan Oleh</th>
                        <th class="py-3 text-center pe-4" style="width:110px">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sertifikat as $item)
                    @php
                        $pendaftaran = $item->pendaftaran;
                        $peserta     = $pendaftaran?->peserta;
                        $pel         = $pendaftaran?->pelatihan;
                        $namaPeserta = trim(($pendaftaran->first_name ?? '') . ' ' . ($pendaftaran->last_name ?? ''));
                    @endphp
                    <tr>
                        <td class="ps-4">
                            <span class="badge bg-secondary-subtle text-secondary-emphasis fw-semibold font-monospace"
                                  style="font-size:12px;letter-spacing:.03em">
                                {{ $item->kode_sertifikat }}
                            </span>
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="rounded-circle bg-primary-subtle text-primary fw-bold
                                            d-flex align-items-center justify-content-center"
                                     style="width:32px;height:32px;font-size:12px;flex-shrink:0">
                                    {{ strtoupper(substr($namaPeserta ?: '?', 0, 1)) }}
                                </div>
                                <span class="fw-semibold small">{{ $namaPeserta ?: '-' }}</span>
                            </div>
                        </td>
                        <td class="small">{{ $pel->nama_pelatihan ?? '-' }}</td>
                        <td class="small text-muted text-nowrap">
                            {{ $item->tgl_terbit ? \Carbon\Carbon::parse($item->tgl_terbit)->translatedFormat('j M Y') : '-' }}
                        </td>
                        <td class="small text-muted">{{ $item->diterbitkan_oleh ?? '-' }}</td>
                        <td class="text-center pe-4">
                            <div class="d-flex justify-content-center gap-1">
                                {{-- Preview --}}
                                <button type="button"
                                    class="btn btn-sm btn-outline-secondary rounded-3"
                                    title="Preview"
                                    onclick="openModalPreview({{ json_encode([
                                        'kode'             => $item->kode_sertifikat,
                                        'nama_peserta'     => $namaPeserta,
                                        'pelatihan'        => $pel->nama_pelatihan ?? '-',
                                        'kode_pelatihan'   => $pel->kode_pelatihan ?? '-',
                                        'instruktur'       => $pel->instruktur->nama ?? '-',
                                        'tgl_terbit'       => $item->tgl_terbit ? \Carbon\Carbon::parse($item->tgl_terbit)->translatedFormat('j M Y') : '-',
                                        'diterbitkan_oleh' => $item->diterbitkan_oleh ?? '-',
                                        'persen_hadir'     => $pendaftaran->kualifikasiSertifikasi->persen_hadir ?? null,
                                        'file'             => $item->file ? asset('storage/'.$item->file) : null,
                                    ]) }})">
                                    <i class="bi bi-eye"></i>
                                </button>
                                {{-- Download --}}
                                @if($item->file && \Illuminate\Support\Facades\Storage::disk('public')->exists($item->file))
                                <a href="{{ asset('storage/'.$item->file) }}"
                                   download="{{ $item->kode_sertifikat }}.pdf"
                                   class="btn btn-sm btn-outline-primary rounded-3"
                                   title="Download PDF">
                                    <i class="bi bi-download"></i>
                                </a>
                                @else
                                <button class="btn btn-sm btn-outline-secondary rounded-3" disabled title="File tidak ada">
                                    <i class="bi bi-download"></i>
                                </button>
                                @endif
                                {{-- Hapus --}}
                                <form action="{{ route('admin.sertifikat.destroy', $item->id_sertifikat) }}"
                                      method="POST"
                                      onsubmit="return confirm('Hapus sertifikat {{ $item->kode_sertifikat }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-3" title="Hapus">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="bi bi-patch-check fs-2 d-block mb-2"></i>
                            Belum ada sertifikat yang diterbitkan.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Pagination --}}
    @if($sertifikat->hasPages())
    <div class="card-footer bg-white border-top py-3 px-4 d-flex justify-content-between align-items-center">
        <small class="text-muted">
            Menampilkan {{ $sertifikat->firstItem() }}–{{ $sertifikat->lastItem() }}
            dari {{ $sertifikat->total() }} sertifikat
        </small>
        {{ $sertifikat->links() }}
    </div>
    @endif
</div>


{{-- ══════════════════════════════════════════════
     MODAL TERBITKAN & GENERATE SERTIFIKAT
══════════════════════════════════════════════ --}}
<div class="modal fade" id="modalTerbitkan" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
        <div class="modal-content border-0 shadow rounded-4">
            <div class="modal-header border-0 px-4 pt-4 pb-2">
                <h5 class="modal-title fw-bold d-flex align-items-center gap-2">
                    <i class="bi bi-patch-check text-primary"></i>
                    Terbitkan &amp; Generate Sertifikat
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body px-4 pt-2 pb-0">
                <p class="text-muted small mb-3">
                    Peserta berikut memenuhi syarat kehadiran &ge; 80%. Pilih untuk diterbitkan sertifikat.
                </p>

                {{-- Filter dalam modal --}}
                <div class="d-flex gap-2 mb-3">
                    <input type="text" id="searchKandidatInput"
                        class="form-control rounded-3 flex-fill"
                        placeholder="Cari nama peserta..."
                        oninput="filterKandidat()">
                    <select id="filterPelatihanKandidat" class="form-select rounded-3" style="max-width:200px"
                            onchange="filterKandidat()">
                        <option value="">Semua Pelatihan</option>
                        @foreach($pelatihan as $p)
                            <option value="{{ $p->id_pelatihan }}">{{ $p->nama_pelatihan }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Tabel kandidat --}}
                <div class="table-responsive rounded-3 border mb-4" style="max-height:260px;overflow-y:auto">
                    <table class="table table-sm align-middle mb-0" id="tabelKandidat">
                        <thead class="table-light sticky-top">
                            <tr>
                                <th style="width:36px" class="ps-3">
                                    <input type="checkbox" id="checkAll" class="form-check-input"
                                           onchange="toggleCheckAll(this)">
                                </th>
                                <th class="py-2">Nama Peserta</th>
                                <th class="py-2">Kode Sertifikat</th>
                                <th class="py-2">Pelatihan</th>
                                <th class="py-2 text-center">% Hadir</th>
                                <th class="py-2 text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody id="bodyKandidat">
                            @php
                                $kandidat = \App\Models\KualifikasiSertifikasiModel::with([
                                    'pendaftaran.peserta',
                                    'pendaftaran.pelatihan',
                                    'pendaftaran.sertifikat',
                                ])->get();
                            @endphp
                            @forelse($kandidat as $k)
                            @php
                                $pd      = $k->pendaftaran;
                                $sudah   = $pd?->sertifikat !== null;
                                $layak   = $k->memenuhi_syarat;
                                $nama    = trim(($pd->first_name ?? '') . ' ' . ($pd->last_name ?? ''));
                                $pelNama = $pd?->pelatihan?->nama_pelatihan ?? '-';
                                $pelId   = $pd?->pelatihan_id ?? '';
                                $kode    = $pd?->sertifikat?->kode_sertifikat ?? '— belum terbit —';
                            @endphp
                            <tr data-nama="{{ strtolower($nama) }}" data-pelatihan="{{ $pelId }}"
                                class="{{ !$layak ? 'table-light text-muted' : '' }}">
                                <td class="ps-3">
                                    <input type="checkbox" name="pendaftaran_ids[]"
                                           value="{{ $pd?->id_pendaftaran }}"
                                           class="form-check-input kandidat-check"
                                           {{ !$layak || $sudah ? 'disabled' : '' }}
                                           {{ $layak && !$sudah ? 'checked' : '' }}>
                                </td>
                                <td class="small fw-semibold">{{ $nama ?: '-' }}</td>
                                <td class="small font-monospace text-muted">{{ $kode }}</td>
                                <td class="small">{{ $pelNama }}</td>
                                <td class="text-center small">{{ $k->persen_hadir ?? '-' }}%</td>
                                <td class="text-center">
                                    @if($layak)
                                        <span class="badge bg-success-subtle text-success-emphasis rounded-pill px-2">Layak</span>
                                    @else
                                        <span class="badge bg-danger-subtle text-danger-emphasis rounded-pill px-2">Tidak Layak</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-3 text-muted small">
                                    Tidak ada kandidat sertifikasi.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Upload Template --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold small">
                        Upload Template Sertifikat
                        <span class="text-muted fw-normal">(PNG/PDF/DOCX)</span>
                    </label>
                    <div class="border border-dashed rounded-3 text-center py-4 px-3"
                         style="border-style:dashed!important;cursor:pointer"
                         onclick="document.getElementById('templateFile').click()">
                        <i class="bi bi-cloud-upload fs-3 text-muted d-block mb-1"></i>
                        <div class="small text-muted">Klik atau seret file template sertifikat</div>
                        <div class="text-muted" style="font-size:11px">Maks. 5 MB</div>
                        <div id="namaFileTemplate" class="small text-primary mt-1 fw-semibold"></div>
                    </div>
                    <input type="file" id="templateFile" class="d-none"
                           accept=".png,.pdf,.docx"
                           onchange="tampilkanNamaFile(this)">
                </div>

                {{-- Tanggal & Diterbitkan Oleh --}}
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold small">Tanggal Terbit</label>
                        <input type="date" id="tgl_terbit_generate"
                               name="tgl_terbit"
                               class="form-control rounded-3"
                               value="{{ now()->format('Y-m-d') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold small">Diterbitkan Oleh</label>
                        <input type="text" id="diterbitkan_oleh_generate"
                               name="diterbitkan_oleh"
                               class="form-control rounded-3"
                               value="Administrator SIMPERTI">
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2 pb-4">
                    <button type="button" class="btn btn-outline-secondary px-4 rounded-3"
                            data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-outline-primary px-4 rounded-3 fw-semibold"
                            onclick="openModalPreviewGenerate()">
                        <i class="bi bi-eye me-1"></i> Preview Sertifikat
                    </button>
                    <button type="button" class="btn btn-primary px-4 rounded-3 fw-semibold"
                            onclick="submitGenerateMassal()">
                        <i class="bi bi-patch-check me-1"></i> Generate &amp; Terbitkan
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Form hidden untuk generate massal --}}
<form id="formGenerateMassal" method="POST" style="display:none">
    @csrf
    <input type="hidden" name="diterbitkan_oleh" id="hidden_diterbitkan_oleh">
</form>


{{-- ══════════════════════════════════════════════
     MODAL PREVIEW SERTIFIKAT
══════════════════════════════════════════════ --}}
<div class="modal fade" id="modalPreview" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
        <div class="modal-content border-0 shadow rounded-4">
            <div class="modal-header border-0 px-4 pt-4 pb-2">
                <h5 class="modal-title fw-bold d-flex align-items-center gap-2">
                    <i class="bi bi-eye text-primary"></i> Preview Sertifikat
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body px-4 pt-2 pb-0">
                <p class="text-muted small mb-3">
                    Tampilan sertifikat yang akan di-generate. Kode unik masing-masing peserta tercantum di bagian bawah.
                </p>

                {{-- Preview Card --}}
                <div class="rounded-3 border p-4 mb-4 text-center"
                     style="background:linear-gradient(135deg,#fff8f7,#fff);min-height:340px">

                    {{-- Ikon trophy --}}
                    <div class="mb-2" style="font-size:2.5rem">🏆</div>

                    <div class="fw-bold text-uppercase mb-1" style="font-size:1.1rem;letter-spacing:.1em">
                        Sertifikat Kelulusan
                    </div>
                    <div class="text-muted small text-uppercase mb-3" style="letter-spacing:.08em">
                        Certificate of Completion
                    </div>

                    <hr class="mx-auto" style="width:60px;border-top:2px solid #e84e3a">

                    <div class="text-muted small mb-1 mt-3">Diberikan kepada</div>
                    <div class="fw-bold fst-italic mb-3" id="prev_nama_peserta"
                         style="font-size:1.5rem;color:#e84e3a;font-family:Georgia,serif">—</div>

                    <div class="small text-muted mb-1">
                        telah berhasil menyelesaikan dan dinyatakan<br>lulus dalam program pelatihan
                    </div>
                    <div class="fw-bold mb-1" id="prev_pelatihan" style="font-size:1rem">—</div>
                    <div class="text-muted small mb-4" id="prev_meta">—</div>

                    {{-- Footer sertifikat --}}
                    <div class="d-flex justify-content-between align-items-end mt-3 pt-3 border-top">
                        <div class="text-start">
                            <div class="fw-bold small" id="prev_diterbitkan_oleh">—</div>
                            <div class="text-muted" style="font-size:11px">Diterbitkan Oleh</div>
                        </div>
                        <div class="text-center">
                            <div class="font-monospace fw-bold small text-muted" id="prev_kode"
                                 style="font-size:11px">—</div>
                            <div class="text-muted" style="font-size:10px">Kode Sertifikat Unik</div>
                        </div>
                        <div class="text-end">
                            <div class="fw-bold small" id="prev_instruktur">—</div>
                            <div class="text-muted" style="font-size:11px">Instruktur Pelaksana</div>
                        </div>
                    </div>

                    <div class="text-muted mt-3" style="font-size:10px">
                        Verifikasi: simperti.id/cek-sertifikat &nbsp;|&nbsp; SIMPERTI &copy; {{ date('Y') }}
                    </div>
                </div>

                {{-- Link file jika sudah ada --}}
                <div id="prev_download_section" class="d-none mb-3 text-center">
                    <a id="prev_download_link" href="#" target="_blank"
                       class="btn btn-sm btn-outline-primary rounded-3">
                        <i class="bi bi-file-earmark-pdf me-1"></i> Buka PDF
                    </a>
                </div>

                <div class="d-flex justify-content-end gap-2 pb-4">
                    <button type="button" class="btn btn-outline-secondary px-4 rounded-3"
                            data-bs-dismiss="modal">Kembali</button>
                    <button type="button" class="btn btn-primary px-4 rounded-3 fw-semibold"
                            id="btnGenerateDariPreview"
                            onclick="submitGenerateMassal()">
                        <i class="bi bi-patch-check me-1"></i> Generate Semua Sertifikat
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .border-dashed { border: 2px dashed #dee2e6 !important; }
    .sticky-top { position: sticky; top: 0; z-index: 1; }
</style>
@endpush

@push('scripts')
<script>
    // ── Buka modal terbitkan ──
    function openModalTerbitkan() {
        new bootstrap.Modal(document.getElementById('modalTerbitkan')).show();
    }

    // ── Preview dari tabel (sertifikat yang sudah terbit) ──
    function openModalPreview(data) {
        // Set konten preview
        document.getElementById('prev_nama_peserta').textContent    = data.nama_peserta  || '—';
        document.getElementById('prev_pelatihan').textContent       = data.pelatihan     || '—';
        document.getElementById('prev_instruktur').textContent      = data.instruktur    || '—';
        document.getElementById('prev_diterbitkan_oleh').textContent= data.diterbitkan_oleh || '—';
        document.getElementById('prev_kode').textContent            = data.kode          || '—';

        const hadir = data.persen_hadir !== null ? data.persen_hadir + '%' : '—';
        document.getElementById('prev_meta').textContent =
            `${data.tgl_terbit} | Kehadiran: ${hadir} | Kode Pelatihan: ${data.kode_pelatihan}`;

        // Tampilkan link download jika ada file
        const dlSection = document.getElementById('prev_download_section');
        const dlLink    = document.getElementById('prev_download_link');
        if (data.file) {
            dlSection.classList.remove('d-none');
            dlLink.href = data.file;
        } else {
            dlSection.classList.add('d-none');
        }

        // Sembunyikan tombol generate karena ini preview sertifikat yang sudah terbit
        document.getElementById('btnGenerateDariPreview').classList.add('d-none');

        new bootstrap.Modal(document.getElementById('modalPreview')).show();
    }

    // ── Preview dari modal generate (sebelum terbit) ──
    function openModalPreviewGenerate() {
        const checked = document.querySelectorAll('.kandidat-check:checked');
        if (checked.length === 0) {
            alert('Pilih minimal satu peserta untuk dipreview.');
            return;
        }

        // Ambil data baris pertama yang dicentang
        const row    = checked[0].closest('tr');
        const cells  = row.querySelectorAll('td');
        const nama   = cells[1].textContent.trim();
        const kode   = cells[2].textContent.trim();
        const pel    = cells[3].textContent.trim();
        const hadir  = cells[4].textContent.trim();

        const tgl    = document.getElementById('tgl_terbit_generate').value;
        const oleh   = document.getElementById('diterbitkan_oleh_generate').value;

        const tglFmt = tgl ? new Date(tgl).toLocaleDateString('id-ID',
            { day:'numeric', month:'long', year:'numeric' }) : '—';

        document.getElementById('prev_nama_peserta').textContent     = nama;
        document.getElementById('prev_pelatihan').textContent        = pel;
        document.getElementById('prev_instruktur').textContent       = '—';
        document.getElementById('prev_diterbitkan_oleh').textContent = oleh;
        document.getElementById('prev_kode').textContent             = kode !== '— belum terbit —' ? kode : '(akan digenerate)';
        document.getElementById('prev_meta').textContent             =
            `${tglFmt} | Kehadiran: ${hadir} | —`;

        document.getElementById('prev_download_section').classList.add('d-none');
        document.getElementById('btnGenerateDariPreview').classList.remove('d-none');

        // Tutup modal terbitkan, buka preview
        bootstrap.Modal.getInstance(document.getElementById('modalTerbitkan'))?.hide();
        setTimeout(() => {
            new bootstrap.Modal(document.getElementById('modalPreview')).show();
        }, 300);
    }

    // ── Submit generate massal ──
    function submitGenerateMassal() {
        const checked = document.querySelectorAll('.kandidat-check:checked');
        if (checked.length === 0) {
            alert('Pilih minimal satu peserta untuk diterbitkan.');
            return;
        }
        if (!confirm(`Generate & terbitkan ${checked.length} sertifikat?`)) return;

        const oleh = document.getElementById('diterbitkan_oleh_generate').value;
        if (!oleh.trim()) {
            alert('Isi field "Diterbitkan Oleh" terlebih dahulu.');
            return;
        }

        // Ambil pelatihan_id dari baris pertama yang dicentang untuk route massal
        const row       = checked[0].closest('tr');
        const pelId     = row.dataset.pelatihan;

        const form = document.getElementById('formGenerateMassal');
        form.action = `/admin/sertifikat/generate-massal/${pelId}`;
        document.getElementById('hidden_diterbitkan_oleh').value = oleh;

        // Hapus input lama
        form.querySelectorAll('input[name="pendaftaran_ids[]"]').forEach(e => e.remove());
        checked.forEach(cb => {
            const inp = document.createElement('input');
            inp.type  = 'hidden';
            inp.name  = 'pendaftaran_ids[]';
            inp.value = cb.value;
            form.appendChild(inp);
        });

        form.submit();
    }

    // ── Check all ──
    function toggleCheckAll(el) {
        document.querySelectorAll('.kandidat-check:not([disabled])')
            .forEach(cb => cb.checked = el.checked);
    }

    // ── Filter kandidat dalam modal ──
    function filterKandidat() {
        const search = document.getElementById('searchKandidatInput').value.toLowerCase();
        const pelId  = document.getElementById('filterPelatihanKandidat').value;

        document.querySelectorAll('#bodyKandidat tr').forEach(row => {
            const nama = row.dataset.nama ?? '';
            const pel  = row.dataset.pelatihan ?? '';
            const matchNama = nama.includes(search);
            const matchPel  = !pelId || pel === pelId;
            row.style.display = (matchNama && matchPel) ? '' : 'none';
        });
    }

    // ── Tampilkan nama file yang diupload ──
    function tampilkanNamaFile(input) {
        const el = document.getElementById('namaFileTemplate');
        el.textContent = input.files.length ? input.files[0].name : '';
    }
</script>
@endpush

@endsection