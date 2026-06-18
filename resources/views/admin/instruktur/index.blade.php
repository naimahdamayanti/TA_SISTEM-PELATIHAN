@extends('layouts.admin')

@section('title', 'Kelola Instruktur')

@section('content')

<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="fw-bold mb-1">Kelola Instruktur</h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 small">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Kelola Instruktur</li>
            </ol>
        </nav>
    </div>
</div>

{{-- ── Alert ──────────────────────────────────────────────────────────────── --}}
@foreach(['success','error','warning','info'] as $type)
    @if(session($type))
        <div class="alert alert-{{ $type === 'error' ? 'danger' : $type }} alert-dismissible fade show rounded-3 mb-4" role="alert">
            <i class="bi bi-{{ $type === 'success' ? 'check-circle-fill' : ($type === 'error' ? 'exclamation-circle-fill' : 'info-circle-fill') }} me-2"></i>
            {{ session($type) }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
@endforeach

{{-- ── Banner instruktur menunggu verifikasi ──────────────────────────────── --}}
@php $menunggu = $instruktur->where('status_verifikasi', 'menunggu')->count(); @endphp
@if($menunggu > 0)
    <div class="alert alert-warning d-flex align-items-center gap-2 rounded-3 mb-4">
        <i class="bi bi-hourglass-split fs-5"></i>
        <div>
            Terdapat <strong>{{ $menunggu }} instruktur</strong> yang menunggu verifikasi dokumen.
            Periksa dokumen mereka dan buat keputusan di bawah.
        </div>
    </div>
@endif

{{-- ── Search ──────────────────────────────────────────────────────────────── --}}
<div class="card border-0 shadow-sm rounded-3 mb-4">
    <div class="card-body py-3">
        <form method="GET" action="{{ route('admin.instruktur.index') }}" class="row g-2 align-items-center">
            <div class="col-12 col-md-6">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0">
                        <i class="bi bi-search text-muted"></i>
                    </span>
                    <input type="text" name="search" value="{{ request('search') }}"
                        class="form-control border-start-0 ps-0"
                        placeholder="Cari nama, email, atau username...">
                </div>
            </div>
            <div class="col-auto d-flex gap-2">
                <button type="submit" class="btn btn-primary px-4">Cari</button>
                <a href="{{ route('admin.instruktur.index') }}" class="btn btn-outline-secondary" title="Reset">
                    <i class="bi bi-arrow-counterclockwise"></i>
                </a>
            </div>
        </form>
    </div>
</div>

{{-- ── Grid Instruktur ─────────────────────────────────────────────────────── --}}
@php
    $avatarColors = [
        'linear-gradient(135deg,#e84e3a,#c0392b)',
        'linear-gradient(135deg,#3498db,#2980b9)',
        'linear-gradient(135deg,#2ecc71,#27ae60)',
        'linear-gradient(135deg,#f1c40f,#f39c12)',
        'linear-gradient(135deg,#9b59b6,#8e44ad)',
        'linear-gradient(135deg,#1abc9c,#16a085)',
    ];
@endphp

<div class="row g-4">
    @forelse($instruktur as $idx => $ins)
    @php
        $color    = $avatarColors[$idx % count($avatarColors)];
        $pelCount = $ins->pelatihan_count ?? 0;
        $isActive = $pelCount > 0;
        $status   = $ins->status_verifikasi; // null | menunggu | terverifikasi | ditolak
    @endphp
    <div class="col-12 col-md-6 col-xl-4">
        <div class="card border-0 shadow-sm rounded-4 h-100 text-center px-3 py-4
                    {{ $status === 'menunggu' ? 'border border-warning border-2' : '' }}"
             style="{{ $status === 'menunggu' ? 'border-color:#f59e0b!important' : '' }}">

            {{-- Ribbon menunggu --}}
            @if($status === 'menunggu')
            <div class="position-absolute top-0 start-0 m-2">
                <span class="badge rounded-pill px-2 py-1"
                      style="background:#fff8e1;color:#92400e;border:1px solid #f59e0b;font-size:11px">
                    <i class="bi bi-hourglass-split me-1"></i>Menunggu Verifikasi
                </span>
            </div>
            @endif

            {{-- Avatar --}}
            <div class="mx-auto mb-3 mt-2 rounded-circle d-flex align-items-center justify-content-center
                        text-white fw-bold"
                 style="width:72px;height:72px;font-size:1.5rem;background:{{ $color }}">
                {{ strtoupper(substr($ins->nama, 0, 2)) }}
            </div>

            {{-- Info --}}
            <div class="fw-bold fs-6 mb-1">{{ $ins->nama }}</div>
            <div class="text-muted small mb-2">{{ $ins->email }}</div>

            {{-- Badge status aktif mengajar --}}
            @if($isActive)
                <span class="badge rounded-pill mb-1 px-3 py-1"
                      style="background:#fff0ee;color:#e84e3a;border:1px solid #f5c6c0;font-size:12px">
                    Sedang Mengajar
                </span>
            @else
                <span class="badge rounded-pill mb-1 px-3 py-1"
                      style="background:#f5f5f5;color:#888;border:1px solid #e0e0e0;font-size:12px">
                    Tidak Aktif
                </span>
            @endif

            {{-- Badge status verifikasi --}}
            <div class="mb-3">
                @if($status === 'terverifikasi' || is_null($status))
                    <span class="badge rounded-pill px-2 py-1"
                          style="background:#f0fdf4;color:#15803d;border:1px solid #bbf7d0;font-size:11px">
                        <i class="bi bi-shield-check me-1"></i>Terverifikasi
                    </span>
                @elseif($status === 'menunggu')
                    {{-- sudah tampil di ribbon atas --}}
                    @if($ins->bukti_penerimaan)
                    <a href="{{ Storage::url($ins->bukti_penerimaan) }}" target="_blank"
                       class="btn btn-sm btn-outline-secondary px-2 py-0 mt-1"
                       style="font-size:11px">
                        <i class="bi bi-file-earmark-text me-1"></i>Lihat Bukti
                    </a>
                    @endif
                @elseif($status === 'ditolak')
                    <span class="badge rounded-pill px-2 py-1"
                          style="background:#fef2f2;color:#dc2626;border:1px solid #fecaca;font-size:11px"
                          title="{{ $ins->catatan_verifikasi }}">
                        <i class="bi bi-x-circle me-1"></i>Ditolak
                    </span>
                @endif
            </div>

            {{-- Statistik --}}
            <div class="d-flex justify-content-center gap-4 mb-3">
                <div>
                    <div class="fw-bold fs-5 lh-1">{{ $pelCount }}</div>
                    <div class="text-muted" style="font-size:11px">Pelatihan</div>
                </div>
                <div>
                    <div class="fw-bold fs-5 lh-1">
                        {{ $ins->pelatihan->sum(fn($p) => $p->pendaftaran->where('status','diterima')->count()) }}
                    </div>
                    <div class="text-muted" style="font-size:11px">Peserta</div>
                </div>
                <div>
                    <div class="fw-bold fs-5 lh-1">
                        {{ $ins->pelatihan->where('status','selesai')->count() }}
                    </div>
                    <div class="text-muted" style="font-size:11px">Selesai</div>
                </div>
            </div>

            {{-- No HP --}}
            <div class="text-muted small mb-3">
                {{ $ins->no_hp ?? 'No HP belum diisi' }}
            </div>

            {{-- ── Aksi ───────────────────────────────────────────────────── --}}
            <div class="d-flex justify-content-center gap-2 mt-auto flex-wrap">

                {{-- Tombol Verifikasi — hanya muncul jika status menunggu --}}
                @if($status === 'menunggu')
                <button type="button"
                    class="btn btn-warning btn-sm px-3 rounded-3 fw-semibold"
                    onclick="openModalVerifikasi(
                        {{ $ins->id_user }},
                        '{{ addslashes($ins->nama) }}',
                        '{{ route('admin.instruktur.verifikasi', $ins->id_user) }}'
                    )">
                    <i class="bi bi-shield-check me-1"></i>Verifikasi
                </button>
                @endif

                {{-- Tombol Tugaskan — nonaktif jika belum terverifikasi --}}
                @if($status === 'terverifikasi' || is_null($status))
                <button type="button"
                    class="btn btn-primary btn-sm px-3 rounded-3"
                    onclick="openModalTugaskan({{ $ins->id_user }}, '{{ addslashes($ins->nama) }}')">
                    <i class="bi bi-person-check me-1"></i>Tugaskan
                </button>
                @else
                <button type="button" class="btn btn-primary btn-sm px-3 rounded-3 disabled"
                        title="Instruktur belum terverifikasi" disabled>
                    <i class="bi bi-person-check me-1"></i>Tugaskan
                </button>
                @endif

                {{-- Hapus --}}
                <form action="{{ route('admin.instruktur.destroy', $ins->id_user) }}"
                      method="POST"
                      onsubmit="return confirm('Hapus instruktur {{ addslashes($ins->nama) }}?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger btn-sm px-3 rounded-3" title="Hapus">
                        <i class="bi bi-trash"></i>
                    </button>
                </form>

            </div>

        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-body text-center py-5 text-muted">
                <i class="bi bi-person-x fs-2 d-block mb-2"></i>
                Belum ada instruktur terdaftar.
            </div>
        </div>
    </div>
    @endforelse
</div>

{{-- Pagination --}}
@if($instruktur->hasPages())
<div class="d-flex justify-content-between align-items-center mt-4">
    <small class="text-muted">
        Menampilkan {{ $instruktur->firstItem() }}–{{ $instruktur->lastItem() }}
        dari {{ $instruktur->total() }} instruktur
    </small>
    {{ $instruktur->links() }}
</div>
@endif


{{-- ══════════════════════════════════════════════════════════════════════════
     MODAL VERIFIKASI INSTRUKTUR
══════════════════════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="modalVerifikasi" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-4">
            <div class="modal-header border-0 px-4 pt-4 pb-2">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-shield-check text-warning me-2"></i>Verifikasi Instruktur
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body px-4 pt-2 pb-0">
                <p class="text-muted small mb-3">
                    Pastikan Anda sudah memeriksa bukti penerimaan instruktur
                    <strong id="namaInstrukturVerif"></strong> sebelum membuat keputusan.
                </p>

                <form method="POST" id="formVerifikasi">
                    @csrf @method('PATCH')

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Keputusan <span class="text-danger">*</span></label>
                        <div class="d-flex gap-3">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="status_verifikasi"
                                       id="radioTerima" value="terverifikasi" required>
                                <label class="form-check-label text-success fw-semibold" for="radioTerima">
                                    <i class="bi bi-check-circle me-1"></i>Terverifikasi
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="status_verifikasi"
                                       id="radioTolak" value="ditolak">
                                <label class="form-check-label text-danger fw-semibold" for="radioTolak">
                                    <i class="bi bi-x-circle me-1"></i>Ditolak
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold small">
                            Catatan
                            <span class="text-muted fw-normal">(wajib diisi jika ditolak)</span>
                        </label>
                        <textarea name="catatan_verifikasi" class="form-control rounded-3" rows="3"
                                  placeholder="Contoh: Dokumen tidak sesuai / tidak terbaca"></textarea>
                    </div>

                    <div class="d-flex justify-content-end gap-2 pb-4">
                        <button type="button" class="btn btn-outline-secondary px-4 rounded-3"
                                data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-warning px-4 rounded-3 fw-semibold">
                            <i class="bi bi-check-lg me-1"></i>Simpan Keputusan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


{{-- ══════════════════════════════════════════════════════════════════════════
     MODAL TUGASKAN KE PELATIHAN
══════════════════════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="modalTugaskan" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-4">
            <div class="modal-header border-0 px-4 pt-4 pb-2">
                <h5 class="modal-title fw-bold">Tugaskan ke Pelatihan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body px-4 pt-2 pb-0">
                <p class="text-muted small mb-4">Pilih pelatihan yang akan diampu oleh instruktur ini.</p>
                <form action="{{ route('admin.instruktur.tugaskan') }}" method="POST" id="formTugaskan">
                    @csrf
                    <input type="hidden" name="instruktur_id" id="tugaskan_instruktur_id">

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Instruktur</label>
                        <input type="text" id="tugaskan_instruktur_nama"
                            class="form-control rounded-3 bg-light" readonly>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold small">Pelatihan yang Ditugaskan</label>
                        <select name="pelatihan_id" class="form-select rounded-3" required>
                            <option value="">-- Pilih Pelatihan --</option>
                            @foreach($pelatihan as $p)
                                <option value="{{ $p->id_pelatihan }}">
                                    {{ $p->nama_pelatihan }} ({{ $p->kode_pelatihan }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="d-flex justify-content-end gap-2 pb-4">
                        <button type="button" class="btn btn-outline-secondary px-4 rounded-3"
                                data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary px-4 rounded-3 fw-semibold">
                            <i class="bi bi-check-lg me-1"></i>Simpan Penugasan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // ── Modal Verifikasi ───────────────────────────────────────────────────
    function openModalVerifikasi(id, nama, actionUrl) {
        document.getElementById('namaInstrukturVerif').textContent = nama;
        document.getElementById('formVerifikasi').action           = actionUrl;
        // Reset radio
        document.querySelectorAll('#formVerifikasi input[type=radio]')
                .forEach(r => r.checked = false);
        document.querySelector('#formVerifikasi textarea').value = '';
        new bootstrap.Modal(document.getElementById('modalVerifikasi')).show();
    }

    // ── Modal Tugaskan ─────────────────────────────────────────────────────
    function openModalTugaskan(id, nama) {
        document.getElementById('tugaskan_instruktur_id').value   = id;
        document.getElementById('tugaskan_instruktur_nama').value = nama;
        new bootstrap.Modal(document.getElementById('modalTugaskan')).show();
    }
</script>
@endpush

@endsection