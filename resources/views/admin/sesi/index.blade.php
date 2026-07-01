@extends('layouts.admin')

@section('title', 'Jadwal Sesi Pelatihan')

@section('content')

{{-- Breadcrumb + Header --}}
<div class="d-flex align-items-start justify-content-between mb-4">
    <div>
        <h4 class="fw-bold mb-1">Kelola Pelatihan</h4>
        <nav aria-label="breadcrumb" class="mb-1">
            <ol class="breadcrumb mb-0 small">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.pelatihan.index') }}">Kelola Pelatihan</a></li>
                <li class="breadcrumb-item active">Jadwal Sesi — {{ $pelatihan->nama_pelatihan }}</li>
            </ol>
        </nav>
        <h4 class="fw-bold mb-1">Jadwal Sesi: {{ $pelatihan->nama_pelatihan }}</h4>
        <div class="d-flex align-items-center gap-3 small text-muted flex-wrap">
            <span>Kode: <strong class="text-dark font-monospace">{{ $pelatihan->kode_pelatihan }}</strong></span>
            <span>&middot;</span>
            <span>Instruktur: <strong class="text-dark">{{ $pelatihan->instruktur->nama ?? '-' }}</strong></span>
            <span>&middot;</span>
            <span>Kuota: <strong class="text-dark">{{ $pelatihan->kuota }}</strong></span>
        </div>
    </div>
    <div class="d-flex align-items-center gap-2 mt-1">
        @php
            $sc = match($pelatihan->status) {
                'tersedia' => 'success', 'penuh' => 'warning', 'selesai' => 'secondary', default => 'secondary'
            };
        @endphp
        <span class="badge bg-{{ $sc }}-subtle text-{{ $sc }}-emphasis px-3 py-2 rounded-pill">
            {{ ucfirst($pelatihan->status) }}
        </span>
        <button type="button" class="btn btn-primary px-4" onclick="openModalTambahSesi()">
            <i class="bi bi-plus-lg me-1"></i> Tambah Sesi
        </button>
    </div>
</div>

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

<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm rounded-3 h-100">
            <div class="card-body d-flex align-items-center gap-3 py-3 px-4">
                <div class="rounded-3 d-flex align-items-center justify-content-center text-white"
                     style="width:46px;height:46px;background:linear-gradient(135deg,#e84e3a,#c0392b);flex-shrink:0">
                    <i class="bi bi-calendar3 fs-5"></i>
                </div>
                <div>
                    <div class="fw-bold" style="font-size:1.6rem;line-height:1">{{ $sesi->count() }}</div>
                    <div class="small fw-semibold">Total Sesi</div>
                    <div class="text-muted" style="font-size:11px">Sesi terjadwal</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm rounded-3 h-100">
            <div class="card-body d-flex align-items-center gap-3 py-3 px-4">
                <div class="rounded-3 d-flex align-items-center justify-content-center text-white"
                     style="width:46px;height:46px;background:linear-gradient(135deg,#2ecc71,#27ae60);flex-shrink:0">
                    <i class="bi bi-calendar-check fs-5"></i>
                </div>
                <div>
                    <div class="fw-bold" style="font-size:1.6rem;line-height:1">
                        {{ $sesi->filter(fn($s) => $s->logbook->count() > 0)->count() }}
                    </div>
                    <div class="small fw-semibold">Sesi Berlangsung</div>
                    <div class="text-muted" style="font-size:11px">Terisi logbook</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm rounded-3 h-100">
            <div class="card-body d-flex align-items-center gap-3 py-3 px-4">
                <div class="rounded-3 d-flex align-items-center justify-content-center text-white"
                     style="width:46px;height:46px;background:linear-gradient(135deg,#3498db,#2980b9);flex-shrink:0">
                    <i class="bi bi-people fs-5"></i>
                </div>
                <div>
                    <div class="fw-bold" style="font-size:1.6rem;line-height:1">{{ $pesertaDiterima }}</div>
                    <div class="small fw-semibold">Peserta Diterima</div>
                    <div class="text-muted" style="font-size:11px">Dari tabel pendaftaran</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm rounded-3 h-100">
            <div class="card-body d-flex align-items-center gap-3 py-3 px-4">
                <div class="rounded-3 d-flex align-items-center justify-content-center text-white"
                     style="width:46px;height:46px;background:linear-gradient(135deg,#f1c40f,#f39c12);flex-shrink:0">
                    <i class="bi bi-geo-alt fs-5"></i>
                </div>
                <div>
                    <div class="fw-bold" style="font-size:1.6rem;line-height:1">
                        {{ $sesi->pluck('lokasi')->filter()->unique()->count() }}
                    </div>
                    <div class="small fw-semibold">Lokasi Variatif</div>
                    <div class="text-muted" style="font-size:11px">Tempat pelaksanaan</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="d-flex flex-column gap-3">
    @forelse($sesi as $idx => $s)
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-body d-flex align-items-center gap-4 py-3 px-4">
            <div class="rounded-3 d-flex align-items-center justify-content-center text-white fw-bold flex-shrink-0"
                 style="width:42px;height:42px;font-size:14px;background:linear-gradient(135deg,#e84e3a,#c0392b)">
                S{{ $idx + 1 }}
            </div>

            <div class="flex-fill min-w-0">
                <div class="fw-semibold text-dark mb-1">{{ $s->judul_sesi }}</div>
                <div class="d-flex align-items-center gap-3 text-muted small flex-wrap">
                    <span>
                        <i class="bi bi-calendar-date me-1"></i>
                        {{ \Carbon\Carbon::parse($s->tanggal)->translatedFormat('j M Y') }}
                    </span>
                    <span>
                        <i class="bi bi-clock me-1"></i>
                        {{ \Carbon\Carbon::parse($s->waktu_mulai)->format('H:i') }}
                        &ndash;
                        {{ \Carbon\Carbon::parse($s->waktu_selesai)->format('H:i') }}
                    </span>
                    @if($s->lokasi)
                    <span>
                        <i class="bi bi-geo-alt me-1"></i>{{ $s->lokasi }}
                    </span>
                    @endif
                </div>
            </div>

            <div class="d-flex gap-2 flex-shrink-0">
                <button type="button"
                    class="btn btn-sm btn-outline-primary rounded-3"
                    onclick="openModalEditSesi({{ json_encode($s) }})"
                    title="Edit Sesi">
                    <i class="bi bi-pencil"></i>
                </button>
                <form action="{{ route('admin.sesi.destroy', [$pelatihan->id_pelatihan, $s->id_sesi]) }}"
                      method="POST"
                      onsubmit="return confirm('Hapus sesi ini?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-3" title="Hapus Sesi">
                        <i class="bi bi-trash"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>
    @empty
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-body text-center py-5 text-muted">
            <i class="bi bi-calendar-x fs-2 d-block mb-2"></i>
            Belum ada sesi. Klik <strong>Tambah Sesi</strong> untuk memulai.
        </div>
    </div>
    @endforelse
</div>

<div class="modal fade" id="modalTambahSesi" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-4">
            <div class="modal-header border-0 px-4 pt-4 pb-2">
                <h5 class="modal-title fw-bold">Tambah Sesi Pelatihan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body px-4 pt-2 pb-0">
                <form action="{{ route('admin.sesi.store', $pelatihan->id_pelatihan) }}" method="POST">
                    @csrf
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold small">Judul Sesi</label>
                            <input type="text" name="judul_sesi"
                                class="form-control rounded-3"
                                placeholder="Contoh: Pengenalan APD"
                                value="{{ old('judul_sesi') }}" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold small">Tanggal</label>
                            <input type="date" name="tanggal"
                                class="form-control rounded-3"
                                min="{{ $pelatihan->tgl_mulai->format('Y-m-d') }}"
                                max="{{ $pelatihan->tgl_selesai->format('Y-m-d') }}"
                                value="{{ old('tanggal') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Waktu Mulai</label>
                            <input type="time" name="waktu_mulai"
                                class="form-control rounded-3"
                                value="{{ old('waktu_mulai', '08:00') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Waktu Selesai</label>
                            <input type="time" name="waktu_selesai"
                                class="form-control rounded-3"
                                value="{{ old('waktu_selesai', '16:00') }}" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold small">Lokasi</label>
                            <input type="text" name="lokasi"
                                class="form-control rounded-3"
                                placeholder="Ruang Pelatihan A / Online"
                                value="{{ old('lokasi') }}">
                        </div>
                    </div>
                    <div class="d-flex justify-content-end gap-2 py-4">
                        <button type="button" class="btn btn-outline-secondary px-4 rounded-3"
                                data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary px-4 rounded-3 fw-semibold">
                            <i class="bi bi-plus-lg me-1"></i> Simpan Sesi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalEditSesi" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-4">
            <div class="modal-header border-0 px-4 pt-4 pb-2">
                <h5 class="modal-title fw-bold">Edit Sesi Pelatihan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body px-4 pt-2 pb-0">
                <form id="formEditSesi" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold small">Judul Sesi</label>
                            <input type="text" name="judul_sesi" id="edit_judul_sesi"
                                class="form-control rounded-3" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold small">Tanggal</label>
                            <input type="date" name="tanggal" id="edit_tanggal"
                                class="form-control rounded-3"
                                min="{{ $pelatihan->tgl_mulai->format('Y-m-d') }}"
                                max="{{ $pelatihan->tgl_selesai->format('Y-m-d') }}"
                                required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Waktu Mulai</label>
                            <input type="time" name="waktu_mulai" id="edit_waktu_mulai"
                                class="form-control rounded-3" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Waktu Selesai</label>
                            <input type="time" name="waktu_selesai" id="edit_waktu_selesai"
                                class="form-control rounded-3" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold small">Lokasi</label>
                            <input type="text" name="lokasi" id="edit_lokasi"
                                class="form-control rounded-3"
                                placeholder="Ruang Pelatihan A / Online">
                        </div>
                    </div>
                    <div class="d-flex justify-content-end gap-2 py-4">
                        <button type="button" class="btn btn-outline-secondary px-4 rounded-3"
                                data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary px-4 rounded-3 fw-semibold">
                            <i class="bi bi-save me-1"></i> Simpan Sesi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function openModalTambahSesi() {
        new bootstrap.Modal(document.getElementById('modalTambahSesi')).show();
    }

    function openModalEditSesi(sesi) {
        const f = document.getElementById('formEditSesi');
        f.action = `/admin/pelatihan/{{ $pelatihan->id_pelatihan }}/sesi/${sesi.id_sesi}`;

        document.getElementById('edit_judul_sesi').value   = sesi.judul_sesi ?? '';
        document.getElementById('edit_tanggal').value      = sesi.tanggal ? sesi.tanggal.substring(0,10) : '';
        document.getElementById('edit_waktu_mulai').value  = sesi.waktu_mulai ? sesi.waktu_mulai.substring(0,5) : '';
        document.getElementById('edit_waktu_selesai').value= sesi.waktu_selesai ? sesi.waktu_selesai.substring(0,5) : '';
        document.getElementById('edit_lokasi').value       = sesi.lokasi ?? '';

        new bootstrap.Modal(document.getElementById('modalEditSesi')).show();
    }

    @if($errors->any())
        openModalTambahSesi();
    @endif
</script>
@endpush
@endsection