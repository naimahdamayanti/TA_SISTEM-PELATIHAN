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
                    <div class="text-muted" style="font-size:11px">Kehadiran &lt; 60%</div>
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

<div class="card border-0 shadow-sm rounded-3">

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

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4 py-3">Kode Sertifikat</th>
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
                    Peserta berikut memenuhi syarat kehadiran &ge; 60%. Pilih untuk diterbitkan sertifikat.
                </p>

                <div class="d-flex gap-2 mb-3">
                    <input type="text" id="searchKandidatInput"
                        class="form-control rounded-3 flex-fill"
                        placeholder="Cari nama peserta..."
                        oninput="filterKandidat()">
                    <select id="filterPelatihanKandidat" class="form-select rounded-3" style="max-width:200px"
                            onchange="filterKandidat()">
                        <option value="">Semua Pelatihan</option>
                        @foreach($pelatihan as $p)
                            <option value="{{ $p->id_pelatihan }}"
                                    data-has-template="{{ $p->template_sertifikat ? '1' : '0' }}"
                                    data-template-url="{{ $p->template_sertifikat ? Storage::url($p->template_sertifikat) : '' }}">
                                {{ $p->nama_pelatihan }}
                            </option>
                        @endforeach
                    </select>
                </div>

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
                                $layak   = $k->layakSertifikat();
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

                <div class="mb-3">
                    <label class="form-label fw-semibold small">
                        Template Sertifikat
                        <span class="text-muted fw-normal">(PNG/JPG · Landscape A4)</span>
                    </label>

                    <div id="previewTemplateAda" class="rounded-3 border p-2 text-center bg-light mb-2" style="display:none">
                        <img id="imgTemplateAda" src="" style="max-height:80px;object-fit:contain" alt="template">
                        <div class="small text-success mt-1">
                            <i class="bi bi-check-circle me-1"></i>Template sudah ada · akan diganti jika upload baru
                        </div>
                    </div>

                    <form id="formUploadTemplate" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="border rounded-3 text-center py-3 px-3"
                            style="border-style:dashed!important;cursor:pointer;border-color:#dee2e6"
                            onclick="document.getElementById('templateFile').click()">
                            <i class="bi bi-cloud-upload fs-4 text-muted d-block mb-1"></i>
                            <div class="small text-muted">Klik untuk upload template baru</div>
                            <div class="text-muted" style="font-size:11px">Maks. 5 MB</div>
                            <div id="namaFileTemplate" class="small text-primary mt-1 fw-semibold"></div>
                        </div>
                        <input type="file" id="templateFile" name="template" class="d-none"
                            accept=".png,.jpg,.jpeg"
                            onchange="tampilkanNamaFile(this)">
                        <button type="button" class="btn btn-outline-primary btn-sm mt-2 w-100 rounded-3"
                                onclick="submitUploadTemplate()">
                            <i class="bi bi-upload me-1"></i> Simpan Template untuk Pelatihan Ini
                        </button>
                        <button type="button" class="btn btn-outline-secondary btn-sm mt-2 w-100 rounded-3" onclick="openModalPosisi()">
                            <i class="bi bi-arrows-move me-1"></i> Atur Posisi Teks
                        </button>
                    </form>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold small">
                        Tanda Tangan
                        <span class="text-muted fw-normal">(PNG transparan lebih bagus)</span>
                    </label>
                    <form id="formUploadTandaTangan" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="border rounded-3 text-center py-2 px-3"
                            style="border-style:dashed!important;cursor:pointer;border-color:#dee2e6"
                            onclick="document.getElementById('ttdFile').click()">
                            <i class="bi bi-pen fs-5 text-muted d-block mb-1"></i>
                            <div class="small text-muted">Klik untuk upload tanda tangan</div>
                            <div id="namaTtdFile" class="small text-primary mt-1 fw-semibold"></div>
                        </div>
                        <input type="file" id="ttdFile" name="tanda_tangan" class="d-none"
                            accept=".png,.jpg,.jpeg"
                            onchange="document.getElementById('namaTtdFile').textContent = this.files[0]?.name ?? ''">
                        <button type="button" class="btn btn-outline-secondary btn-sm mt-2 w-100 rounded-3"
                                onclick="submitUploadTandaTangan()">
                            <i class="bi bi-upload me-1"></i> Simpan Tanda Tangan
                        </button>
                    </form>
                </div>

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

<div class="modal fade" id="modalPosisi" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-xl">
    <div class="modal-content border-0 shadow rounded-4">
      <div class="modal-header border-0 px-4 pt-4 pb-2">
        <h5 class="modal-title fw-bold">
            <i class="bi bi-arrows-move text-primary"></i> Atur Posisi Teks pada Template
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body px-4">
        <p class="text-muted small mb-3">
            Pilih data di kiri, lalu klik pada gambar template untuk menentukan posisinya.
        </p>
        <div class="row g-3">
          <div class="col-md-3">
            <div class="list-group" id="posisiFieldList">
              <button type="button" class="list-group-item list-group-item-action active" data-field="nama_peserta">Nama Peserta</button>
              <button type="button" class="list-group-item list-group-item-action" data-field="nama_pelatihan">Nama Pelatihan</button>
              <button type="button" class="list-group-item list-group-item-action" data-field="nomor_sertifikat">Nomor Sertifikat</button>
              <button type="button" class="list-group-item list-group-item-action" data-field="tanda_tangan">Tanda Tangan</button>
              <button type="button" class="list-group-item list-group-item-action" data-field="tgl_terbit">Tanggal Terbit</button>
              <button type="button" class="list-group-item list-group-item-action" data-field="diterbitkan_oleh">Diterbitkan Oleh</button>
              <button type="button" class="list-group-item list-group-item-action" data-field="kode">Kode Sertifikat</button>
            </div>
          </div>
          <div class="col-md-9">
            <div id="posisiCanvas" style="position:relative;width:100%;aspect-ratio:297/210;border:1px solid #dee2e6;overflow:hidden;cursor:crosshair;">
              <img id="posisiTemplateImg" src="" style="width:100%;height:100%;object-fit:fill;display:block;">
              <div id="posisiMarkers" style="position:absolute;top:0;left:0;width:100%;height:100%;"></div>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer border-0 px-4 pb-4">
        <button type="button" class="btn btn-outline-secondary rounded-3" data-bs-dismiss="modal">Tutup</button>
        <button type="button" class="btn btn-primary rounded-3" onclick="simpanPosisi()">Simpan Posisi</button>
      </div>
    </div>
  </div>
</div>

<form id="formGenerateMassal" method="POST" style="display:none">
    @csrf
    <input type="hidden" name="diterbitkan_oleh" id="hidden_diterbitkan_oleh">
</form>

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

                <div class="rounded-3 border mb-4 overflow-hidden position-relative"
                    style="min-height:340px;background:#f9f9f9">

                    <img id="prev_template_img" src="" alt="template"
                        style="width:100%;max-height:400px;object-fit:contain;display:none">

                    <div id="prev_fallback"
                        style="min-height:340px;display:flex;align-items:center;justify-content:center;flex-direction:column">
                        <i class="bi bi-image fs-1 text-muted d-block mb-2"></i>
                        <div class="text-muted small">Belum ada template untuk pelatihan ini</div>
                        <div class="text-muted" style="font-size:11px">Upload template terlebih dahulu</div>
                    </div>

                    <div id="prev_pdf_embed" class="d-none mb-3" style="height:380px;border:1px solid #dee2e6;border-radius:8px;overflow:hidden;">
                        <iframe id="prev_pdf_iframe" src="" style="width:100%;height:100%;border:0;"></iframe>
                    </div>

                    <div class="p-3 border-top bg-white" id="prev_info_box" style="display:none">
                        <div class="row text-center g-2">
                            <div class="col-4">
                                <div class="text-muted" style="font-size:10px">Nama Peserta</div>
                                <div class="fw-bold small" id="prev_nama_peserta">—</div>
                            </div>
                            <div class="col-4">
                                <div class="text-muted" style="font-size:10px">Pelatihan</div>
                                <div class="fw-bold small" id="prev_pelatihan">—</div>
                            </div>
                            <div class="col-4">
                                <div class="text-muted" style="font-size:10px">Info</div>
                                <div class="small text-muted" id="prev_meta">—</div>
                            </div>
                        </div>
                        <hr class="my-2">
                        <div class="row text-center g-2">
                            <div class="col-4">
                                <div class="text-muted" style="font-size:10px">Diterbitkan Oleh</div>
                                <div class="small fw-semibold" id="prev_diterbitkan_oleh">—</div>
                            </div>
                            <div class="col-4">
                                <div class="text-muted" style="font-size:10px">Kode Sertifikat</div>
                                <div class="small font-monospace text-muted" id="prev_kode">—</div>
                            </div>
                            <div class="col-4">
                                <div class="text-muted" style="font-size:10px">Instruktur</div>
                                <div class="small fw-semibold" id="prev_instruktur">—</div>
                            </div>
                        </div>
                    </div>
                </div>

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
    const routeUploadTemplate  = "{{ route('admin.sertifikat.uploadTemplate', ['pelatihan' => '__ID__']) }}";
    const routeGenerateMassal  = "{{ route('admin.sertifikat.generateMassal') }}";
    const routeGetPosisi  = "{{ route('admin.sertifikat.getPosisi', ['pelatihan' => '__ID__']) }}";
    const routeSavePosisi = "{{ route('admin.sertifikat.savePosisi', ['pelatihan' => '__ID__']) }}";
    const ALIGN_PER_FIELD = {
    nama_peserta: 'center', nama_pelatihan: 'center', nomor_sertifikat: 'center', 
    tgl_terbit: 'left', diterbitkan_oleh: 'right', kode: 'center', tanda_tangan: 'center',
    };
    const LABEL_PER_FIELD = {
        nama_peserta: 'Nama', nama_pelatihan: 'Pelatihan', nomor_sertifikat: 'No. Sertifikat', 
        tgl_terbit: 'Tgl Terbit', diterbitkan_oleh: 'Diterbitkan Oleh', kode: 'Kode', tanda_tangan: 'Tanda Tangan',
    };
    const routeUploadTandaTangan = "{{ route('admin.sertifikat.uploadTandaTangan', ['pelatihan' => '__ID__']) }}";

    function submitUploadTandaTangan() {
        const pelId = document.getElementById('filterPelatihanKandidat').value;
        if (!pelId) {
            alert('Pilih pelatihan di filter atas terlebih dahulu.');
            return;
        }
        const file = document.getElementById('ttdFile').files[0];
        if (!file) {
            alert('Pilih file tanda tangan terlebih dahulu.');
            return;
        }
        const form = document.getElementById('formUploadTandaTangan');
        form.action = routeUploadTandaTangan.replace('__ID__', pelId);
        form.submit();
    }

    let currentPelatihanIdForPosisi = null;
    let posisiData = {};
    let activeField = 'nama_peserta';

    function openModalPosisi() {
        const pelId = document.getElementById('filterPelatihanKandidat').value;
        if (!pelId) {
            alert('Pilih pelatihan di filter atas terlebih dahulu.');
            return;
        }
        currentPelatihanIdForPosisi = pelId;

        fetch(routeGetPosisi.replace('__ID__', pelId))
            .then(res => res.json())
            .then(data => {
                if (!data.template_url) {
                    alert('Upload template untuk pelatihan ini terlebih dahulu.');
                    return;
                }
                document.getElementById('posisiTemplateImg').src = data.template_url;
                posisiData = data.posisi;
                renderMarkers();
                new bootstrap.Modal(document.getElementById('modalPosisi')).show();
            });
    }

    document.getElementById('posisiFieldList').addEventListener('click', e => {
        const btn = e.target.closest('[data-field]');
        if (!btn) return;
        document.querySelectorAll('#posisiFieldList .list-group-item').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        activeField = btn.dataset.field;
        renderMarkers();
    });

    document.getElementById('posisiCanvas').addEventListener('click', e => {
        const rect = e.currentTarget.getBoundingClientRect();
        const x = ((e.clientX - rect.left) / rect.width) * 100;
        const y = ((e.clientY - rect.top) / rect.height) * 100;

        posisiData[activeField] = {
            x: Math.round(x * 100) / 100,
            y: Math.round(y * 100) / 100,
            align: ALIGN_PER_FIELD[activeField],
        };
        renderMarkers();
    });

    function renderMarkers() {
        const container = document.getElementById('posisiMarkers');
        container.innerHTML = '';
        Object.entries(posisiData).forEach(([field, pos]) => {
            const marker = document.createElement('div');
            marker.textContent = LABEL_PER_FIELD[field] || field;
            marker.style.position = 'absolute';
            marker.style.left = pos.x + '%';
            marker.style.top = pos.y + '%';
            marker.style.transform = 'translate(-50%, -50%)';
            marker.style.background = (field === activeField) ? '#1565C0' : 'rgba(0,0,0,0.6)';
            marker.style.color = '#fff';
            marker.style.fontSize = '10px';
            marker.style.padding = '2px 6px';
            marker.style.borderRadius = '4px';
            marker.style.whiteSpace = 'nowrap';
            marker.style.pointerEvents = 'none';
            container.appendChild(marker);
        });
    }

    function simpanPosisi() {
        const formData = new FormData();
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);
        Object.entries(posisiData).forEach(([field, pos]) => {
            formData.append(`posisi[${field}][x]`, pos.x);
            formData.append(`posisi[${field}][y]`, pos.y);
            formData.append(`posisi[${field}][align]`, pos.align);
        });

        fetch(routeSavePosisi.replace('__ID__', currentPelatihanIdForPosisi), {
            method: 'POST', body: formData,
        })
            .then(res => res.json())
            .then(() => {
                alert('Posisi berhasil disimpan.');
                bootstrap.Modal.getInstance(document.getElementById('modalPosisi')).hide();
            });
    }
    function openModalTerbitkan() {
        new bootstrap.Modal(document.getElementById('modalTerbitkan')).show();
    }

    function submitUploadTemplate() {
        const pelId = document.getElementById('filterPelatihanKandidat').value;
        if (!pelId) {
            alert('Pilih pelatihan di filter atas terlebih dahulu.');
            return;
        }
        const file = document.getElementById('templateFile').files[0];
        if (!file) {
            alert('Pilih file template terlebih dahulu.');
            return;
        }

        const form = document.getElementById('formUploadTemplate');
        form.action = routeUploadTemplate.replace('__ID__', pelId);
        form.submit();
    }

    function openModalPreviewGenerate() {
        const checked = document.querySelectorAll('.kandidat-check:checked');
        if (checked.length === 0) {
            alert('Pilih minimal satu peserta untuk dipreview.');
            return;
        }

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

        const candidatePelId = row.dataset.pelatihan;
        const select  = document.getElementById('filterPelatihanKandidat');
        const matchOpt = Array.from(select.options)
            .find(o => o.value === candidatePelId);
        const templateUrl = matchOpt?.dataset.templateUrl ?? '';

        const templateImg = document.getElementById('prev_template_img');
        const fallback    = document.getElementById('prev_fallback');
        const infoBox     = document.getElementById('prev_info_box');

        if (templateUrl) {
            templateImg.src           = templateUrl;
            templateImg.style.display = 'block';
            fallback.style.display    = 'none';
            infoBox.style.display     = 'block';
        } else {
            templateImg.style.display = 'none';
            fallback.style.display    = 'flex';
            infoBox.style.display     = 'none';
        }

        document.getElementById('prev_nama_peserta').textContent     = nama;
        document.getElementById('prev_pelatihan').textContent        = pel;
        document.getElementById('prev_instruktur').textContent       = '—';
        document.getElementById('prev_diterbitkan_oleh').textContent = oleh;
        document.getElementById('prev_kode').textContent             = kode !== '— belum terbit —' ? kode : '(akan digenerate)';

        let metaText = `${tglFmt} | Hadir: ${hadir}`;
        if (checked.length > 1) {
            metaText += ` | +${checked.length - 1} peserta lain akan menggunakan template masing-masing`;
        }
        document.getElementById('prev_meta').textContent = metaText;

        document.getElementById('prev_download_section').classList.add('d-none');
        document.getElementById('btnGenerateDariPreview').classList.remove('d-none');

        bootstrap.Modal.getInstance(document.getElementById('modalTerbitkan'))?.hide();
        setTimeout(() => {
            new bootstrap.Modal(document.getElementById('modalPreview')).show();
        }, 300);
    }

    function openModalPreview(data) {
        const templateImg = document.getElementById('prev_template_img');
        const fallback    = document.getElementById('prev_fallback');
        const infoBox     = document.getElementById('prev_info_box');

        if (data.file) {
            templateImg.style.display = 'none';
            fallback.style.display    = 'none';
            infoBox.style.display     = 'block';

            document.getElementById('prev_pdf_iframe').src = data.file;
            document.getElementById('prev_pdf_embed').classList.remove('d-none');
        } else {
            templateImg.style.display = 'none';
            fallback.style.display    = 'flex';
            infoBox.style.display     = 'none';
            document.getElementById('prev_pdf_embed').classList.add('d-none');
        }

        document.getElementById('prev_nama_peserta').textContent     = data.nama_peserta     || '—';
        document.getElementById('prev_pelatihan').textContent        = data.pelatihan        || '—';
        document.getElementById('prev_instruktur').textContent       = data.instruktur       || '—';
        document.getElementById('prev_diterbitkan_oleh').textContent = data.diterbitkan_oleh || '—';
        document.getElementById('prev_kode').textContent             = data.kode             || '—';

        const hadir = data.persen_hadir !== null ? data.persen_hadir + '%' : '—';
        document.getElementById('prev_meta').textContent =
            `${data.tgl_terbit} | Kehadiran: ${hadir} | ${data.kode_pelatihan}`;

        const dlSection = document.getElementById('prev_download_section');
        const dlLink    = document.getElementById('prev_download_link');
        if (data.file) {
            dlSection.classList.remove('d-none');
            dlLink.href = data.file;
        } else {
            dlSection.classList.add('d-none');
        }

        document.getElementById('btnGenerateDariPreview').classList.add('d-none');
        new bootstrap.Modal(document.getElementById('modalPreview')).show();
    }

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

        const form = document.getElementById('formGenerateMassal');
        form.action = routeGenerateMassal;
        document.getElementById('hidden_diterbitkan_oleh').value = oleh;

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

    function toggleCheckAll(el) {
        document.querySelectorAll('.kandidat-check:not([disabled])')
            .forEach(cb => cb.checked = el.checked);
    }

    function filterKandidat() {
        const search = document.getElementById('searchKandidatInput').value.toLowerCase();
        const pelId  = document.getElementById('filterPelatihanKandidat').value;

        document.querySelectorAll('#bodyKandidat tr').forEach(row => {
            const nama     = row.dataset.nama ?? '';
            const pel      = row.dataset.pelatihan ?? '';
            const matchNama = nama.includes(search);
            const matchPel  = !pelId || pel === pelId;
            row.style.display = (matchNama && matchPel) ? '' : 'none';
        });

        updatePreviewTemplate();
    }

    function updatePreviewTemplate() {
        const select = document.getElementById('filterPelatihanKandidat');
        const opt    = select.options[select.selectedIndex];
        const hasTemplate = opt?.dataset.hasTemplate === '1';
        const url         = opt?.dataset.templateUrl ?? '';

        const previewDiv = document.getElementById('previewTemplateAda');
        const img        = document.getElementById('imgTemplateAda');

        if (hasTemplate && url) {
            img.src = url;
            previewDiv.style.display = 'block';
        } else {
            previewDiv.style.display = 'none';
        }
    }

    function tampilkanNamaFile(input) {
        document.getElementById('namaFileTemplate').textContent =
            input.files.length ? input.files[0].name : '';
    }
</script>
@endpush

@endsection