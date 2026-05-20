@extends('layouts.admin')

@section('title', 'Kelola Pelatihan')

@section('content')
<div class="page-header d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="fw-bold mb-1">Kelola Pelatihan</h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 small">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Kelola Pelatihan</li>
            </ol>
        </nav>
    </div>
    <div class="d-flex gap-2">
        <button type="button" class="btn btn-primary px-4" 
                data-bs-toggle="modal" data-bs-target="#modalTambahKategori">
            <i class="bi bi-tags me-1"></i> Tambah Kategori
        </button>
        <button type="button" class="btn btn-primary px-4" onclick="openModalTambah()">
            <i class="bi bi-plus-lg me-1"></i> Tambah Pelatihan
        </button>
    </div>
</div>

{{-- Alert --}}
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show rounded-3 mb-4" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif
@if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show rounded-3 mb-4" role="alert">
        <i class="bi bi-exclamation-circle-fill me-2"></i>{{ $errors->first() }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

{{-- Filter Bar --}}
<div class="card border-0 shadow-sm rounded-3 mb-4">
    <div class="card-body py-3">
        <form method="GET" action="{{ route('admin.pelatihan.index') }}" class="row g-2 align-items-center">
            <div class="col-12 col-md-4">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0">
                        <i class="bi bi-search text-muted"></i>
                    </span>
                    <input type="text" name="search" value="{{ request('search') }}"
                        class="form-control border-start-0 ps-0"
                        placeholder="Cari nama / kode pelatihan...">
                </div>
            </div>
            <div class="col-6 col-md-3">
                <select name="status" class="form-select">
                    <option value="">Semua Status</option>
                    <option value="tersedia" @selected(request('status') === 'tersedia')>Tersedia</option>
                    <option value="penuh"    @selected(request('status') === 'penuh')>Penuh</option>
                    <option value="selesai"  @selected(request('status') === 'selesai')>Selesai</option>
                </select>
            </div>
            <div class="col-6 col-md-3">
                <select name="kategori" class="form-select">
                    <option value="">Semua Kategori</option>
                    @foreach($kategori as $kat)
                        <option value="{{ $kat }}" @selected(request('kategori') === $kat)>{{ $kat }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-primary flex-fill">Filter</button>
                <a href="{{ route('admin.pelatihan.index') }}" class="btn btn-outline-secondary" title="Reset">
                    <i class="bi bi-arrow-counterclockwise"></i>
                </a>
            </div>
        </form>
    </div>
</div>

{{-- Tabel --}}
<div class="card border-0 shadow-sm rounded-3">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4 py-3" style="width:110px">Kode</th>
                        <th class="py-3">Nama Pelatihan</th>
                        <th class="py-3">Instruktur</th>
                        <th class="py-3 text-center" style="width:80px">Kuota</th>
                        <th class="py-3">Periode</th>
                        <th class="py-3">Kategori</th>
                        <th class="py-3 text-center" style="width:100px">Status</th>
                        <th class="py-3 text-center pe-4" style="width:130px">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pelatihan as $item)
                    <tr>
                        <td class="ps-4">
                            <span class="badge bg-secondary-subtle text-secondary-emphasis fw-semibold font-monospace">
                                {{ $item->kode_pelatihan }}
                            </span>
                        </td>
                        <td>
                            <div class="fw-semibold text-dark">{{ $item->nama_pelatihan }}</div>
                            <div class="text-muted small text-truncate" style="max-width:260px">
                                {{ $item->deskripsi }}
                            </div>
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="rounded-circle bg-primary-subtle text-primary fw-bold d-flex align-items-center justify-content-center"
                                    style="width:34px;height:34px;font-size:13px;flex-shrink:0">
                                    {{ strtoupper(substr($item->instruktur->nama ?? '?', 0, 1)) }}
                                </div>
                                <span class="small">{{ $item->instruktur->nama ?? '-' }}</span>
                            </div>
                        </td>
                        <td class="text-center">
                            <span class="fw-semibold">{{ $item->kuota }}</span>
                        </td>
                        <td class="small text-nowrap">
                            @if($item->tgl_mulai)
                                {{ \Carbon\Carbon::parse($item->tgl_mulai)->translatedFormat('j M Y') }}
                                @if($item->tgl_selesai)
                                    &ndash; {{ \Carbon\Carbon::parse($item->tgl_selesai)->translatedFormat('j M Y') }}
                                @endif
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge rounded-pill bg-info-subtle text-info-emphasis">
                                {{ $item->kategori->nama_kategori ?? '-' }}
                            </span>
                        </td>
                        <td class="text-center">
                            @php
                                $statusClass = match($item->status) {
                                    'tersedia' => 'success',
                                    'penuh'    => 'warning',
                                    'selesai'  => 'secondary',
                                    default    => 'secondary',
                                };
                            @endphp
                            <span class="badge bg-{{ $statusClass }}-subtle text-{{ $statusClass }}-emphasis px-3 py-1 rounded-pill">
                                {{ ucfirst($item->status) }}
                            </span>
                        </td>
                        <td class="text-center pe-4">
                            <div class="d-flex justify-content-center gap-1">
                                {{-- Detail / Sesi --}}
                                <a href="{{ route('admin.sesi.index', $item->id_pelatihan) }}"
                                   class="btn btn-sm btn-outline-secondary"
                                   title="Jadwal Sesi">
                                    <i class="bi bi-calendar3"></i>
                                </a>
                                {{-- Edit --}}
                                <button type="button"
                                    class="btn btn-sm btn-outline-primary"
                                    title="Edit"
                                    onclick="openModalEdit(
                                        {{ $item->id_pelatihan }},
                                        '{{ addslashes($item->nama_pelatihan) }}',
                                        '{{ addslashes($item->kode_pelatihan) }}',
                                        {{ $item->kategori_id ?? 'null' }},
                                        {{ $item->kuota }},
                                        {{ $item->instruktur_id ?? 'null' }},
                                        '{{ $item->tgl_mulai ?? '' }}',
                                        '{{ $item->tgl_selesai ?? '' }}',
                                        '{{ $item->status }}',
                                        '{{ addslashes($item->deskripsi ?? '') }}'
                                    )">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                {{-- Hapus --}}
                                <form action="{{ route('admin.pelatihan.destroy', $item->id_pelatihan) }}"
                                      method="POST"
                                      onsubmit="return konfirmasiHapus(event, '{{ $item->nama_pelatihan }}')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                            Belum ada data pelatihan.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($pelatihan->hasPages())
    <div class="card-footer bg-white border-top-0 pt-0 pb-3 px-4 d-flex justify-content-between align-items-center">
        <small class="text-muted">
            Menampilkan {{ $pelatihan->firstItem() }}–{{ $pelatihan->lastItem() }}
            dari {{ $pelatihan->total() }} pelatihan
        </small>
        {{ $pelatihan->links() }}
    </div>
    @endif
</div>

{{-- ══════════════════════════════════════
     MODAL TAMBAH PELATIHAN
══════════════════════════════════════ --}}
<div class="modal fade" id="modalTambah" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
        <div class="modal-content border-0 shadow rounded-4">
            <div class="modal-header border-0 px-4 pt-4 pb-2">
                <h5 class="modal-title fw-bold">Tambah Program Pelatihan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body px-4 pt-2 pb-0">
                <form action="{{ route('admin.pelatihan.store') }}" method="POST">
                    @csrf
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold small">Nama Pelatihan</label>
                            <input type="text" name="nama_pelatihan"
                                class="form-control rounded-3"
                                placeholder="Contoh: K3 Umum Dasar"
                                value="{{ old('nama_pelatihan') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Kode Pelatihan</label>
                            <input type="text" name="kode_pelatihan"
                                class="form-control rounded-3 font-monospace"
                                placeholder="Contoh: K3-001"
                                value="{{ old('kode_pelatihan') }}" maxlength="15" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Kategori</label>
                            <select name="kategori_id" class="form-select rounded-3" required>
                                <option value="">-- Pilih Kategori --</option>
                                @foreach($kategori as $kat)
                                    <option value="{{ $kat->id_kategori }}" 
                                        {{ old('kategori_id', $pelatihan->kategori_id ?? '') == $kat->id_kategori ? 'selected' : '' }}>
                                        [{{ $kat->kode_kategori }}] {{ $kat->nama_kategori }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Kuota Peserta</label>
                            <input type="number" name="kuota"
                                class="form-control rounded-3"
                                placeholder="Maks. 500"
                                value="{{ old('kuota', 20) }}" min="1" max="500" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Instruktur</label>
                            <select name="instruktur_id" class="form-select rounded-3" required>
                                <option value="">-- Pilih Instruktur --</option>
                                @foreach($instruktur as $ins)
                                    <option value="{{ $ins->id_user }}" {{ old('instruktur_id') == $ins->id_user ? 'selected' : '' }}>
                                        {{ $ins->nama }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Tanggal Mulai</label>
                            <input type="date" name="tgl_mulai"
                                class="form-control rounded-3"
                                value="{{ old('tgl_mulai') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Tanggal Selesai</label>
                            <input type="date" name="tgl_selesai"
                                class="form-control rounded-3"
                                value="{{ old('tgl_selesai') }}">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold small">Status</label>
                            <select name="status" class="form-select rounded-3" required>
                                <option value="tersedia" {{ old('status','tersedia') === 'tersedia' ? 'selected':'' }}>Tersedia</option>
                                <option value="penuh"    {{ old('status') === 'penuh' ? 'selected':'' }}>Penuh</option>
                                <option value="selesai"  {{ old('status') === 'selesai' ? 'selected':'' }}>Selesai</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold small">Deskripsi</label>
                            <textarea name="deskripsi" rows="3"
                                class="form-control rounded-3"
                                placeholder="Deskripsi program pelatihan...">{{ old('deskripsi') }}</textarea>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end gap-2 py-4">
                        <button type="button" class="btn btn-outline-secondary px-4 rounded-3"
                                data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary px-4 rounded-3 fw-semibold">
                            <i class="bi bi-plus-lg me-1"></i> Simpan Pelatihan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════
     MODAL TAMBAH KATEGORI
══════════════════════════════════════ --}}
<div class="modal fade" id="modalTambahKategori" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
        <div class="modal-content border-0 shadow rounded-4">
            <div class="modal-header border-0 px-4 pt-4 pb-2">
                <h5 class="modal-title fw-bold">Tambah Kategori</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body px-4 pt-2 pb-0">
                <form action="{{ route('admin.kategori.store') }}" method="POST">
                    @csrf
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold small">Nama Kategori</label>
                            <input type="text" name="nama_kategori"
                                class="form-control rounded-3"
                                placeholder="Contoh: K3 Umum Dasar"
                                value="{{ old('nama_kategori') }}" required>
                        </div>            
                    </div>
                    <div class="d-flex justify-content-end gap-2 py-4">
                        <button type="button" class="btn btn-outline-secondary px-4 rounded-3"
                                data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary px-4 rounded-3 fw-semibold">
                            <i class="bi bi-plus-lg me-1"></i> Simpan Kategori
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════
     MODAL EDIT PELATIHAN
══════════════════════════════════════ --}}
<div class="modal fade" id="modalEdit" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
        <div class="modal-content border-0 shadow rounded-4">
            <div class="modal-header border-0 px-4 pt-4 pb-2">
                <h5 class="modal-title fw-bold">Edit Program Pelatihan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body px-4 pt-2 pb-0">
                <form id="formEdit" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold small">Nama Pelatihan</label>
                            <input type="text" name="nama_pelatihan" id="edit_nama_pelatihan"
                                class="form-control rounded-3" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Kode Pelatihan</label>
                            <input type="text" name="kode_pelatihan" id="edit_kode_pelatihan"
                                class="form-control rounded-3 font-monospace" maxlength="15" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Kategori</label>
                            <select name="kategori_id" id="edit_kategori_id" class="form-select rounded-3" required>
                                <option value="">-- Pilih Kategori --</option>
                                @foreach($kategori as $kat)
                                    <option value="{{ $kat->id_kategori }}">
                                        [{{ $kat->kode_kategori }}] {{ $kat->nama_kategori }}
                                    </option>
                                @endforeach
                            </select>  
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Kuota Peserta</label>
                            <input type="number" name="kuota" id="edit_kuota"
                                class="form-control rounded-3" min="1" max="500" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Instruktur</label>
                            <select name="instruktur_id" id="edit_instruktur_id" class="form-select rounded-3" required>
                                <option value="">-- Pilih Instruktur --</option>
                                @foreach($instruktur as $ins)
                                    <option value="{{ $ins->id_user }}">{{ $ins->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Tanggal Mulai</label>
                            <input type="date" name="tgl_mulai" id="edit_tgl_mulai"
                                class="form-control rounded-3">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Tanggal Selesai</label>
                            <input type="date" name="tgl_selesai" id="edit_tgl_selesai"
                                class="form-control rounded-3">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold small">Status</label>
                            <select name="status" id="edit_status" class="form-select rounded-3" required>
                                <option value="tersedia">Tersedia</option>
                                <option value="penuh">Penuh</option>
                                <option value="selesai">Selesai</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold small">Deskripsi</label>
                            <textarea name="deskripsi" id="edit_deskripsi" rows="3"
                                class="form-control rounded-3"></textarea>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end gap-2 py-4">
                        <button type="button" class="btn btn-outline-secondary px-4 rounded-3"
                                data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary px-4 rounded-3 fw-semibold">
                            <i class="bi bi-save me-1"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function openModalTambah() {
        new bootstrap.Modal(document.getElementById('modalTambah')).show();
    }

    function openModalEdit(id, nama, kode, kategori, kuota, instrukturId, tglMulai, tglSelesai, status, deskripsi) {
        const f = document.getElementById('formEdit');
        f.action = `/admin/pelatihan/${id}`;

        document.getElementById('edit_nama_pelatihan').value = nama;
        document.getElementById('edit_kode_pelatihan').value = kode;
        document.getElementById('edit_kategori_id').value       = kategori;
        document.getElementById('edit_kuota').value          = kuota;
        document.getElementById('edit_instruktur_id').value  = instrukturId;
        document.getElementById('edit_tgl_mulai').value      = tglMulai ? tglMulai.substring(0, 10) : '';
        document.getElementById('edit_tgl_selesai').value    = tglSelesai ? tglSelesai.substring(0, 10) : '';
        document.getElementById('edit_status').value         = status;
        document.getElementById('edit_deskripsi').value      = deskripsi;

        new bootstrap.Modal(document.getElementById('modalEdit')).show();
    }

    function konfirmasiHapus(e, nama) {
        e.preventDefault();
        if (confirm(`Yakin ingin menghapus pelatihan "${nama}"?\nSemua sesi dan pendaftaran terkait akan ikut terhapus.`)) {
            e.target.submit();
        }
    }

    @if($errors->any())
        openModalTambah();
    @endif
</script>
@endpush
@endsection