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

{{-- Search --}}
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

{{-- Grid Instruktur --}}
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
        $color       = $avatarColors[$idx % count($avatarColors)];
        $pelCount    = $ins->pelatihan_count ?? 0;
        $isActive    = $pelCount > 0;
    @endphp
    <div class="col-12 col-md-6 col-xl-4">
        <div class="card border-0 shadow-sm rounded-4 h-100 text-center px-3 py-4">

            {{-- Avatar --}}
            <div class="mx-auto mb-3 rounded-circle d-flex align-items-center justify-content-center
                        text-white fw-bold"
                 style="width:72px;height:72px;font-size:1.5rem;background:{{ $color }}">
                {{ strtoupper(substr($ins->nama, 0, 2)) }}
            </div>

            {{-- Info --}}
            <div class="fw-bold fs-6 mb-1">{{ $ins->nama }}</div>
            <div class="text-muted small mb-2">{{ $ins->email }}</div>

            {{-- Badge status --}}
            @if($isActive)
                <span class="badge rounded-pill mb-3 px-3 py-1"
                      style="background:#fff0ee;color:#e84e3a;border:1px solid #f5c6c0;font-size:12px">
                    Sedang Mengajar
                </span>
            @else
                <span class="badge rounded-pill mb-3 px-3 py-1"
                      style="background:#f5f5f5;color:#888;border:1px solid #e0e0e0;font-size:12px">
                    Tidak Aktif
                </span>
            @endif

            {{-- Statistik --}}
            <div class="d-flex justify-content-center gap-4 mb-3">
                <div>
                    <div class="fw-bold fs-5 lh-1">{{ $pelCount }}</div>
                    <div class="text-muted" style="font-size:11px">Pelatihan</div>
                </div>
                <div>
                    <div class="fw-bold fs-5 lh-1">
                        {{-- Jumlah peserta diterima dari semua pelatihan instruktur ini --}}
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

            {{-- Aksi --}}
            <div class="d-flex justify-content-center gap-2 mt-auto">
                <button type="button"
                    class="btn btn-primary btn-sm px-3 rounded-3"
                    onclick="openModalTugaskan({{ $ins->id_user }}, '{{ addslashes($ins->nama) }}')">
                    <i class="bi bi-person-check me-1"></i> Tugaskan
                </button>
                <form action="{{ route('admin.instruktur.destroy', $ins->id_user) }}"
                      method="POST"
                      onsubmit="return confirm('Hapus instruktur {{ addslashes($ins->nama) }}?')">
                    @csrf
                    @method('DELETE')
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


{{-- ══════════════════════════════════════
     MODAL TUGASKAN KE PELATIHAN
══════════════════════════════════════ --}}
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
                            class="form-control rounded-3 bg-light"
                            readonly>
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
                            <i class="bi bi-check-lg me-1"></i> Simpan Penugasan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // ── Modal Tugaskan ──
    function openModalTugaskan(id, nama) {
        document.getElementById('tugaskan_instruktur_id').value   = id;
        document.getElementById('tugaskan_instruktur_nama').value = nama;
        new bootstrap.Modal(document.getElementById('modalTugaskan')).show();
    }

    // ── Auto buka modal tambah jika ada error validasi ──
    @if($errors->any())
        openModalTambahInstruktur();
    @endif
</script>
@endpush

@endsection