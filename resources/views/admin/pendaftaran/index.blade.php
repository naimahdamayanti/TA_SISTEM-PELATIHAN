@extends('layouts.admin')

@section('title', 'Kelola Pendaftaran')

@section('content')

<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="fw-bold mb-1">Kelola Pendaftaran</h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 small">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Kelola Pendaftaran</li>
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

{{-- Stat Cards --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-4">
        <div class="card border-0 shadow-sm rounded-3 h-100">
            <div class="card-body d-flex align-items-center gap-3 py-3 px-4">
                <div class="rounded-3 d-flex align-items-center justify-content-center text-white"
                     style="width:46px;height:46px;background:linear-gradient(135deg,#f1c40f,#f39c12);flex-shrink:0">
                    <i class="bi bi-hourglass-split fs-5"></i>
                </div>
                <div>
                    <div class="fw-bold" style="font-size:1.6rem;line-height:1">{{ $stats['menunggu'] }}</div>
                    <div class="small fw-semibold">Menunggu</div>
                    <div class="text-muted" style="font-size:11px">Perlu dikonfirmasi</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4">
        <div class="card border-0 shadow-sm rounded-3 h-100">
            <div class="card-body d-flex align-items-center gap-3 py-3 px-4">
                <div class="rounded-3 d-flex align-items-center justify-content-center text-white"
                     style="width:46px;height:46px;background:linear-gradient(135deg,#2ecc71,#27ae60);flex-shrink:0">
                    <i class="bi bi-check-circle fs-5"></i>
                </div>
                <div>
                    <div class="fw-bold" style="font-size:1.6rem;line-height:1">{{ $stats['diterima'] }}</div>
                    <div class="small fw-semibold">Diterima</div>
                    <div class="text-muted" style="font-size:11px">Peserta aktif</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4">
        <div class="card border-0 shadow-sm rounded-3 h-100">
            <div class="card-body d-flex align-items-center gap-3 py-3 px-4">
                <div class="rounded-3 d-flex align-items-center justify-content-center text-white"
                     style="width:46px;height:46px;background:linear-gradient(135deg,#e74c3c,#c0392b);flex-shrink:0">
                    <i class="bi bi-x-circle fs-5"></i>
                </div>
                <div>
                    <div class="fw-bold" style="font-size:1.6rem;line-height:1">{{ $stats['ditolak'] }}</div>
                    <div class="small fw-semibold">Ditolak</div>
                    <div class="text-muted" style="font-size:11px">Tidak lolos seleksi</div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Filter + Tabel --}}
<div class="card border-0 shadow-sm rounded-3">

    {{-- Filter Bar --}}
    <div class="card-body border-bottom py-3 px-4">
        <form method="GET" action="{{ route('admin.pendaftaran.index') }}" class="row g-2 align-items-center">
            <div class="col-12 col-md-4">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0">
                        <i class="bi bi-search text-muted"></i>
                    </span>
                    <input type="text" name="search" value="{{ request('search') }}"
                        class="form-control border-start-0 ps-0"
                        placeholder="Cari nama / email...">
                </div>
            </div>
            <div class="col-6 col-md-3">
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
            <div class="col-6 col-md-3">
                <select name="status" class="form-select">
                    <option value="">Semua Status</option>
                    <option value="menunggu" @selected(request('status') === 'menunggu')>Menunggu</option>
                    <option value="diterima" @selected(request('status') === 'diterima')>Diterima</option>
                    <option value="ditolak"  @selected(request('status') === 'ditolak')>Ditolak</option>
                </select>
            </div>
            <div class="col-12 col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-primary flex-fill">Filter</button>
                <a href="{{ route('admin.pendaftaran.index') }}" class="btn btn-outline-secondary" title="Reset">
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
                        <th class="ps-4 py-3" style="width:90px">#</th>
                        <th class="py-3">Nama Pendaftar</th>
                        <th class="py-3">Email</th>
                        <th class="py-3">Perusahaan</th>
                        <th class="py-3">Pelatihan</th>
                        <th class="py-3">Tanggal Daftar</th>
                        <th class="py-3 text-center" style="width:110px">Status</th>
                        <th class="py-3 text-center pe-4" style="width:160px">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pendaftaran as $item)
                    <tr>
                        {{-- Nomor --}}
                        <td class="ps-4">
                            <span class="badge bg-secondary-subtle text-secondary-emphasis fw-semibold font-monospace">
                                PD-{{ str_pad($item->id_pendaftaran, 3, '0', STR_PAD_LEFT) }}
                            </span>
                        </td>

                        {{-- Nama --}}
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="rounded-circle bg-primary-subtle text-primary fw-bold
                                            d-flex align-items-center justify-content-center"
                                     style="width:34px;height:34px;font-size:13px;flex-shrink:0">
                                    {{ strtoupper(substr($item->first_name ?? '?', 0, 1)) }}
                                </div>
                                <div>
                                    <div class="fw-semibold small">
                                        {{ trim(($item->first_name ?? '') . ' ' . ($item->last_name ?? '')) ?: '-' }}
                                    </div>
                                    <div class="text-muted" style="font-size:11px">
                                        {{ $item->pekerjaan ?? '-' }}
                                    </div>
                                </div>
                            </div>
                        </td>

                        {{-- Email --}}
                        <td class="small text-muted">{{ $item->email ?? '-' }}</td>

                        {{-- Perusahaan --}}
                        <td class="small">{{ $item->perusahaan ?? '-' }}</td>

                        {{-- Pelatihan --}}
                        <td>
                            <div class="fw-semibold small">{{ $item->pelatihan->nama_pelatihan ?? '-' }}</div>
                            <div class="text-muted font-monospace" style="font-size:11px">
                                {{ $item->pelatihan->kode_pelatihan ?? '' }}
                            </div>
                        </td>

                        {{-- Tanggal --}}
                        <td class="small text-nowrap text-muted">
                            {{ $item->tgl_daftar ? \Carbon\Carbon::parse($item->tgl_daftar)->translatedFormat('j M Y') : '-' }}
                        </td>

                        {{-- Status --}}
                        <td class="text-center">
                            @php
                                $sc = match($item->status) {
                                    'diterima' => ['bg' => 'success', 'label' => 'diterima'],
                                    'menunggu' => ['bg' => 'warning', 'label' => 'menunggu'],
                                    'ditolak'  => ['bg' => 'danger',  'label' => 'ditolak'],
                                    default    => ['bg' => 'secondary','label' => $item->status],
                                };
                            @endphp
                            <span class="badge bg-{{ $sc['bg'] }}-subtle text-{{ $sc['bg'] }}-emphasis px-3 py-1 rounded-pill">
                                {{ $sc['label'] }}
                            </span>
                        </td>

                        {{-- Aksi --}}
                        <td class="text-center pe-4">
                            <div class="d-flex justify-content-center align-items-center gap-1">

                                {{-- Terima & Tolak (hanya jika menunggu) --}}
                                @if($item->status === 'menunggu')
                                    <form action="{{ route('admin.pendaftaran.terima', $item->id_pendaftaran) }}"
                                          method="POST"
                                          onsubmit="return confirm('Terima pendaftaran ini?')">
                                        @csrf
                                        <button type="submit"
                                            class="btn btn-sm btn-outline-success rounded-3 px-2"
                                            title="Terima">
                                            Terima
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.pendaftaran.tolak', $item->id_pendaftaran) }}"
                                          method="POST"
                                          onsubmit="return confirm('Tolak pendaftaran ini?')">
                                        @csrf
                                        <button type="submit"
                                            class="btn btn-sm btn-outline-danger rounded-3 px-2"
                                            title="Tolak">
                                            Tolak
                                        </button>
                                    </form>
                                @endif

                                {{-- Detail --}}
                                <a href="{{ route('admin.pendaftaran.show', $item->id_pendaftaran) }}"
                                   class="btn btn-sm btn-outline-secondary rounded-3"
                                   title="Detail">
                                    <i class="bi bi-eye"></i>
                                </a>

                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                            Tidak ada data pendaftaran.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Pagination --}}
    @if($pendaftaran->hasPages())
    <div class="card-footer bg-white border-top py-3 px-4 d-flex justify-content-between align-items-center">
        <small class="text-muted">
            Menampilkan {{ $pendaftaran->firstItem() }}–{{ $pendaftaran->lastItem() }}
            dari {{ $pendaftaran->total() }} pendaftaran
        </small>
        {{ $pendaftaran->links() }}
    </div>
    @endif

</div>

@endsection