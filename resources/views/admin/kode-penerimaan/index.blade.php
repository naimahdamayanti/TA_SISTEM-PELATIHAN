@extends('layouts.admin')

@section('title', 'Kode Penerimaan Instruktur')

@section('content')

{{-- ── Header ──────────────────────────────────────────────────────────────── --}}
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="fw-bold mb-1">Kode Penerimaan Instruktur</h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 small">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Kode Penerimaan</li>
            </ol>
        </nav>
    </div>
    <button class="btn btn-primary px-4 rounded-3"
            data-bs-toggle="modal" data-bs-target="#modalBuatKode">
        <i class="bi bi-plus-lg me-1"></i> Buat Kode
    </button>
</div>

{{-- ── Alert ───────────────────────────────────────────────────────────────── --}}
@foreach(['success','error','warning','info'] as $type)
    @if(session($type))
        <div class="alert alert-{{ $type === 'error' ? 'danger' : $type }} alert-dismissible fade show rounded-3 mb-4">
            <i class="bi bi-{{ $type === 'success' ? 'check-circle-fill' : 'exclamation-circle-fill' }} me-2"></i>
            {{ session($type) }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
@endforeach

{{-- ── Tabel Kode ──────────────────────────────────────────────────────────── --}}
<div class="card border-0 shadow-sm rounded-3">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead style="background:#f8f9fa">
                    <tr>
                        <th class="ps-4 py-3 text-muted fw-semibold" style="font-size:13px">Kode</th>
                        <th class="py-3 text-muted fw-semibold" style="font-size:13px">Peruntukan</th>
                        <th class="py-3 text-muted fw-semibold" style="font-size:13px">Berlaku Hingga</th>
                        <th class="py-3 text-muted fw-semibold" style="font-size:13px">Status</th>
                        <th class="py-3 text-muted fw-semibold" style="font-size:13px">Digunakan Oleh</th>
                        <th class="py-3 text-muted fw-semibold" style="font-size:13px">Dibuat</th>
                        <th class="pe-4 py-3 text-muted fw-semibold text-end" style="font-size:13px">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($kodes as $k)
                    <tr>
                        {{-- Kode --}}
                        <td class="ps-4">
                            <div class="d-flex align-items-center gap-2">
                                <code class="fs-6 fw-bold" style="color:#374151;letter-spacing:1px">
                                    {{ $k->kode }}
                                </code>
                                <button type="button"
                                        class="btn btn-sm btn-link p-0 text-muted"
                                        onclick="salinKode('{{ $k->kode }}', this)"
                                        title="Salin kode">
                                    <i class="bi bi-copy"></i>
                                </button>
                            </div>
                        </td>

                        {{-- Peruntukan --}}
                        <td>
                            <span class="text-truncate d-inline-block" style="max-width:160px">
                                {{ $k->nama_peruntukan ?? '-' }}
                            </span>
                        </td>

                        {{-- Expired --}}
                        <td style="font-size:13px">
                            @if($k->expired_at)
                                <span class="{{ $k->isExpired() ? 'text-danger' : 'text-muted' }}">
                                    {{ $k->expired_at->format('d/m/Y') }}
                                    @if($k->isExpired())
                                        <span class="badge bg-danger ms-1" style="font-size:10px">Expired</span>
                                    @endif
                                </span>
                            @else
                                <span class="text-muted">Tidak ada</span>
                            @endif
                        </td>

                        {{-- Status --}}
                        <td>
                            @if($k->is_used)
                                <span class="badge rounded-pill px-3 py-1"
                                      style="background:#f0fdf4;color:#15803d;border:1px solid #bbf7d0;font-size:12px">
                                    <i class="bi bi-check-circle me-1"></i>Terpakai
                                </span>
                            @elseif($k->isExpired())
                                <span class="badge rounded-pill px-3 py-1"
                                      style="background:#fef2f2;color:#dc2626;border:1px solid #fecaca;font-size:12px">
                                    <i class="bi bi-x-circle me-1"></i>Expired
                                </span>
                            @else
                                <span class="badge rounded-pill px-3 py-1"
                                      style="background:#f0fdf4;color:#16a34a;border:1px solid #86efac;font-size:12px">
                                    <i class="bi bi-circle-fill me-1" style="font-size:7px"></i>Tersedia
                                </span>
                            @endif
                        </td>

                        {{-- Digunakan oleh --}}
                        <td style="font-size:13px">
                            @if($k->pemakainya)
                                <div class="fw-semibold" style="color:#333">{{ $k->pemakainya->nama }}</div>
                                <div class="text-muted" style="font-size:11px">{{ $k->used_at?->format('d/m/Y H:i') }}</div>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>

                        {{-- Dibuat --}}
                        <td class="text-muted" style="font-size:12px">
                            {{ $k->created_at->format('d/m/Y H:i') }}
                        </td>

                        {{-- Aksi --}}
                        <td class="pe-4 text-end">
                            <div class="d-flex justify-content-end gap-2">

                                {{-- Kirim Email — hanya jika belum terpakai & belum expired --}}
                                @if(!$k->is_used && !$k->isExpired())
                                <button type="button"
                                        class="btn btn-sm btn-outline-primary rounded-3 px-3"
                                        onclick="openModalKirim(
                                            '{{ $k->kode }}',
                                            '{{ route('admin.kode-penerimaan.kirim-email', ['kodePenerimaan' => $k->id_kode_penerimaan]) }}'
                                        )"
                                        title="Kirim ke email instruktur">
                                    <i class="bi bi-envelope me-1"></i>Kirim
                                </button>
                                @endif

                                {{-- Hapus — hanya jika belum terpakai --}}
                                @if(!$k->is_used)
                                <form action="{{ route('admin.kode-penerimaan.destroy', ['kodePenerimaan' => $k->id_kode_penerimaan]) }}'
                                      method="POST"
                                      onsubmit="return confirm('Hapus kode {{ $k->kode }}?')">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                            class="btn btn-sm btn-outline-danger rounded-3 px-3"
                                            title="Hapus kode">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                                @endif

                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7">
                            <div class="text-center py-5 text-muted">
                                <i class="bi bi-key fs-2 d-block mb-2"></i>
                                Belum ada kode penerimaan. Klik <strong>Buat Kode</strong> untuk mulai.
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Pagination --}}
@if($kodes->hasPages())
<div class="d-flex justify-content-between align-items-center mt-3">
    <small class="text-muted">
        Menampilkan {{ $kodes->firstItem() }}–{{ $kodes->lastItem() }}
        dari {{ $kodes->total() }} kode
    </small>
    {{ $kodes->links() }}
</div>
@endif


{{-- ══════════════════════════════════════════════════════════════════════════
     MODAL BUAT KODE
══════════════════════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="modalBuatKode" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-4">

            <div class="modal-header border-0 px-4 pt-4 pb-2">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-key me-2 text-primary"></i>Buat Kode Penerimaan
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body px-4 pt-2">
                <form action="{{ route('admin.kode-penerimaan.store') }}" method="POST" id="formBuatKode">
                    @csrf

                    {{-- Jumlah --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">
                            Jumlah Kode <span class="text-danger">*</span>
                        </label>
                        <input type="number" name="jumlah" id="inputJumlah"
                               class="form-control rounded-3" value="1" min="1" max="20" required>
                        <div class="form-text">Maksimal 20 kode sekaligus. Isi email hanya bisa untuk 1 kode.</div>
                    </div>

                    {{-- Peruntukan --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Peruntukan <span class="text-muted fw-normal">(opsional)</span></label>
                        <input type="text" name="nama_peruntukan" class="form-control rounded-3"
                               placeholder="Contoh: Instruktur K3 batch Juli 2026">
                        <div class="form-text">Catatan internal Admin untuk memudahkan pengelolaan.</div>
                    </div>

                    {{-- Berlaku Hingga --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Berlaku Hingga <span class="text-muted fw-normal">(opsional)</span></label>
                        <input type="date" name="expired_at" class="form-control rounded-3"
                               min="{{ date('Y-m-d', strtotime('+1 day')) }}">
                        <div class="form-text">Jika dikosongkan, kode tidak memiliki batas waktu.</div>
                    </div>

                    <hr class="my-3">

                    {{-- Kirim Langsung via Email (hanya muncul jika jumlah = 1) --}}
                    <div id="sectionEmail">
                        <p class="small fw-semibold text-muted mb-2">
                            <i class="bi bi-envelope me-1"></i>
                            Kirim kode langsung ke email instruktur <span class="fw-normal">(opsional)</span>
                        </p>

                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Nama Instruktur</label>
                            <input type="text" name="nama_tujuan" class="form-control rounded-3"
                                   placeholder="Nama lengkap penerima kode">
                        </div>

                        <div class="mb-1">
                            <label class="form-label fw-semibold small">Email Instruktur</label>
                            <input type="email" name="email_tujuan" class="form-control rounded-3"
                                   placeholder="email@instruktur.com">
                            @error('email_tujuan')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-text mb-1">Jika diisi, kode akan otomatis dikirim ke email instruktur beserta petunjuk registrasi.</div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4 pb-2">
                        <button type="button" class="btn btn-outline-secondary px-4 rounded-3"
                                data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary px-4 rounded-3 fw-semibold">
                            <i class="bi bi-key me-1"></i>Generate Kode
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>


{{-- ══════════════════════════════════════════════════════════════════════════
     MODAL KIRIM ULANG EMAIL
══════════════════════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="modalKirimEmail" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-4">

            <div class="modal-header border-0 px-4 pt-4 pb-2">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-envelope me-2 text-primary"></i>Kirim Kode via Email
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body px-4 pt-2">
                <form method="POST" id="formKirimEmail">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Kode yang Dikirim</label>
                        <input type="text" id="labelKodeKirim"
                               class="form-control rounded-3 bg-light fw-bold"
                               style="font-family:monospace;letter-spacing:2px;color:#374151" readonly>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Nama Instruktur <span class="text-muted fw-normal">(opsional)</span></label>
                        <input type="text" name="nama_tujuan" class="form-control rounded-3"
                               placeholder="Nama lengkap penerima">
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold small">Email Instruktur <span class="text-danger">*</span></label>
                        <input type="email" name="email_tujuan" class="form-control rounded-3" required
                               placeholder="email@instruktur.com">
                    </div>

                    <div class="d-flex justify-content-end gap-2 pb-2">
                        <button type="button" class="btn btn-outline-secondary px-4 rounded-3"
                                data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary px-4 rounded-3 fw-semibold">
                            <i class="bi bi-send me-1"></i>Kirim Email
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>


{{-- ── Toast notifikasi salin ──────────────────────────────────────────────── --}}
<div class="position-fixed bottom-0 end-0 p-3" style="z-index:9999">
    <div id="toastSalin" class="toast align-items-center text-white border-0 rounded-3"
         style="background:#374151" role="alert" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body">
                <i class="bi bi-check-circle me-2"></i>Kode disalin ke clipboard!
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto"
                    data-bs-dismiss="toast"></button>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // ── Sembunyikan section email jika jumlah > 1 ──────────────────────────
    document.getElementById('inputJumlah').addEventListener('input', function () {
        const section = document.getElementById('sectionEmail');
        const emailInput = section.querySelector('input[name="email_tujuan"]');
        const namaInput  = section.querySelector('input[name="nama_tujuan"]');

        if (parseInt(this.value) > 1) {
            section.style.opacity   = '0.4';
            section.style.pointerEvents = 'none';
            emailInput.value = '';
            namaInput.value  = '';
        } else {
            section.style.opacity   = '1';
            section.style.pointerEvents = 'auto';
        }
    });

    // ── Salin kode ke clipboard ────────────────────────────────────────────
    function salinKode(kode, btn) {
        navigator.clipboard.writeText(kode).then(() => {
            btn.innerHTML = '<i class="bi bi-check-lg text-success"></i>';
            setTimeout(() => { btn.innerHTML = '<i class="bi bi-copy"></i>'; }, 1500);

            const toast = new bootstrap.Toast(document.getElementById('toastSalin'));
            toast.show();
        });
    }

    // ── Buka modal kirim email ─────────────────────────────────────────────
    function openModalKirim(kode, actionUrl) {
        document.getElementById('labelKodeKirim').value     = kode;
        document.getElementById('formKirimEmail').action    = actionUrl;
        // Reset field
        document.querySelector('#formKirimEmail input[name="email_tujuan"]').value = '';
        document.querySelector('#formKirimEmail input[name="nama_tujuan"]').value  = '';
        new bootstrap.Modal(document.getElementById('modalKirimEmail')).show();
    }

    // ── Buka modal buat kode jika ada error validasi ───────────────────────
    @if($errors->any())
        new bootstrap.Modal(document.getElementById('modalBuatKode')).show();
    @endif
</script>
@endpush

@endsection