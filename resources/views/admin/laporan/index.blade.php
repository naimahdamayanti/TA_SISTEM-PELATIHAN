@extends('layouts.admin')

@section('title', 'Laporan')

@section('content')

<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="fw-bold mb-1">Laporan Data Pendaftaran</h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 small">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Laporan</li>
            </ol>
        </nav>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.laporan.exportPdf', ['tahun' => $tahun]) }}"
        class="btn btn-primary fw-semibold px-4">
            <i class="bi bi-file-earmark-pdf me-2"></i>Export PDF
        </a>
    </div>
</div>

{{-- ── Tabel Laporan Utama ── --}}
<div class="card border-0 shadow-sm rounded-3">

    {{-- Filter bar --}}
    <div class="card-body border-bottom py-3 px-4">
        <form method="GET" action="{{ route('admin.laporan.index') }}"
              class="row g-2 align-items-center" id="formFilter">

            {{-- Pilih Pelatihan --}}
            <div class="col-12 col-md-3">
                <select name="pelatihan_id" class="form-select">
                    <option value="">Semua Pelatihan</option>
                    @foreach($topPelatihan as $p)
                        <option value="{{ $p->id_pelatihan }}"
                            @selected(request('pelatihan_id') == $p->id_pelatihan)>
                            {{ $p->nama_pelatihan }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Tanggal Mulai --}}
            <div class="col-6 col-md-2">
                <input type="date" name="tgl_dari" class="form-control"
                       value="{{ request('tgl_dari', $tahun . '-01-01') }}">
            </div>

            {{-- Tanggal Selesai --}}
            <div class="col-6 col-md-2">
                <input type="date" name="tgl_sampai" class="form-control"
                       value="{{ request('tgl_sampai', $tahun . '-12-31') }}">
            </div>

            {{-- Tahun --}}
            <div class="col-6 col-md-2">
                <select name="tahun" class="form-select">
                    @foreach($tahunTersedia->count() ? $tahunTersedia : [\Carbon\Carbon::now()->year] as $y)
                        <option value="{{ $y }}" @selected($y == $tahun)>{{ $y }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-auto d-flex gap-2">
                <button type="submit" class="btn btn-primary px-4">
                    <i class="bi bi-funnel me-1"></i>Filter
                </button>
                <a href="{{ route('admin.laporan.index') }}" class="btn btn-outline-secondary" title="Reset">
                    <i class="bi bi-arrow-counterclockwise"></i>
                </a>
            </div>

            {{-- Export dropdown --}}
            <div class="col-auto ms-auto">
                <div class="dropdown">
                    <button class="btn btn-outline-secondary dropdown-toggle" type="button"
                            data-bs-toggle="dropdown">
                        <i class="bi bi-download me-1"></i>Export
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><h6 class="dropdown-header">Export CSV</h6></li>
                        @foreach(['pelatihan' => 'Pelatihan', 'pendaftaran' => 'Pendaftaran', 'sertifikat' => 'Sertifikat', 'kehadiran' => 'Kehadiran'] as $jenis => $label)
                        <li>
                            <a class="dropdown-item small" href="#"
                            onclick="exportCSV('{{ $jenis }}', {{ $tahun }})">
                                <i class="bi bi-filetype-csv me-2 text-muted"></i>{{ $label }}
                            </a>
                        </li>
                        @endforeach
                        <li><hr class="dropdown-divider"></li>
                        <li><h6 class="dropdown-header">Export PDF</h6></li>
                        <li>
                            <a class="dropdown-item small"
                            href="{{ route('admin.laporan.exportPdf', ['tahun' => $tahun]) }}">
                                <i class="bi bi-file-earmark-pdf me-2 text-danger"></i>Laporan Pelatihan
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </form>
    </div>

    {{-- Tabel Pelatihan --}}
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4 py-3" style="width:100px">Kode</th>
                        <th class="py-3">Nama Pelatihan</th>
                        <th class="py-3">Instruktur</th>
                        <th class="py-3 text-center" style="width:70px">Kuota</th>
                        <th class="py-3 text-center" style="width:80px">Diterima</th>
                        <th class="py-3 text-center" style="width:85px">Menunggu</th>
                        <th class="py-3 text-center" style="width:75px">Ditolak</th>
                        <th class="py-3 text-center" style="width:85px">Sertifikat</th>
                        <th class="py-3" style="width:190px">Periode</th>
                        <th class="py-3 text-center" style="width:90px">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        // Filter topPelatihan berdasarkan request jika ada
                        $pelatihanFilter = request('pelatihan_id')
                            ? $topPelatihan->where('id_pelatihan', request('pelatihan_id'))
                            : $topPelatihan;
                    @endphp

                    @forelse($pelatihanFilter as $item)
                    @php
                        $diterima  = $item->pendaftaran_count;
                        $menunggu  = \App\Models\PendaftaranModel::where('pelatihan_id', $item->id_pelatihan)->where('status','menunggu')->count();
                        $ditolak   = \App\Models\PendaftaranModel::where('pelatihan_id', $item->id_pelatihan)->where('status','ditolak')->count();
                        $sertif    = \App\Models\SertifikatModel::whereHas('pendaftaran', fn($q) => $q->where('pelatihan_id', $item->id_pelatihan))->count();
                        $statusClass = match($item->status) {
                            'tersedia' => 'success',
                            'penuh'    => 'warning',
                            'selesai'  => 'secondary',
                            default    => 'secondary',
                        };
                    @endphp
                    <tr>
                        <td class="ps-4">
                            <span class="badge bg-secondary-subtle text-secondary-emphasis fw-semibold font-monospace">
                                {{ $item->kode_pelatihan }}
                            </span>
                        </td>
                        <td class="text-bold small">{{ $item->nama_pelatihan }}</td>
                        <td class="small text-bold">{{ $item->instruktur->nama ?? '-' }}</td>
                        <td class="text-center small fw-semibold">{{ $item->kuota }}</td>
                        <td class="text-center">
                            <span class="text-success fw-bold {{ $diterima > 0 ? 'text-success' : 'text-muted' }}">
                                {{ $diterima }}
                            </span>
                        </td>
                        <td class="text-center">
                            <span class="text-warning fw-bold {{ $menunggu > 0 ? 'text-warning' : 'text-muted' }}">
                                {{ $menunggu }}
                            </span>
                        </td>
                        <td class="text-center">
                            <span class="text-danger fw-bold {{ $ditolak > 0 ? 'text-danger' : 'text-muted' }}">
                                {{ $ditolak }}
                            </span>
                        </td>
                        <td class="text-center">
                            <span class="fw-bold {{ $sertif > 0 ? 'text-primary' : 'text-muted' }}">
                                {{ $sertif }}
                            </span>
                        </td>
                        <td class="small text-bold text-nowrap">
                            @if($item->tgl_mulai)
                                {{ \Carbon\Carbon::parse($item->tgl_mulai)->translatedFormat('j M Y') }}
                                @if($item->tgl_selesai)
                                    &ndash; {{ \Carbon\Carbon::parse($item->tgl_selesai)->translatedFormat('j M Y') }}
                                @endif
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <span class="badge bg-{{ $statusClass }}-subtle text-{{ $statusClass }}-emphasis px-3 py-1 rounded-pill">
                                {{ ucfirst($item->status) }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="text-center py-5 text-muted">
                            <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                            Tidak ada data pelatihan untuk filter ini.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Form export hidden --}}
<form id="formExport" method="GET" action="{{ route('admin.laporan.export') }}" style="display:none">
    <input type="hidden" name="jenis"  id="export_jenis">
    <input type="hidden" name="format" value="csv">
    <input type="hidden" name="tahun"  id="export_tahun">
</form>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    // ── Grafik Bulanan ──
    const bulanLabel  = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
    const grafikData  = @json(array_values($grafikBulanan));
    const bulanIni    = new Date().getMonth();

    new Chart(document.getElementById('grafikBulanan'), {
        type: 'bar',
        data: {
            labels: bulanLabel,
            datasets: [{
                label: 'Pelatihan',
                data: grafikData,
                backgroundColor: grafikData.map((_, i) =>
                    i === bulanIni ? '#e84e3a' : 'rgba(232,78,58,.5)'),
                borderRadius: 5,
                borderSkipped: false,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                x: { grid: { display: false }, border: { display: false } },
                y: { grid: { color: '#f0f0f0' }, border: { display: false },
                     ticks: { stepSize: 1, precision: 0 }, beginAtZero: true }
            }
        }
    });

    // ── Grafik Donut Status Pendaftaran ──
    const statusData = @json($pendaftaranPerStatus);
    new Chart(document.getElementById('grafikStatus'), {
        type: 'doughnut',
        data: {
            labels: ['Diterima', 'Menunggu', 'Ditolak'],
            datasets: [{
                data: [
                    statusData['diterima'] ?? 0,
                    statusData['menunggu'] ?? 0,
                    statusData['ditolak']  ?? 0,
                ],
                backgroundColor: ['#2ecc71','#f1c40f','#e74c3c'],
                borderWidth: 0,
                hoverOffset: 6,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '65%',
            plugins: {
                legend: { position: 'bottom', labels: { boxWidth: 12, padding: 16 } }
            }
        }
    });

    // ── Export CSV ──
    function exportCSV(jenis, tahun) {
        document.getElementById('export_jenis').value = jenis;
        document.getElementById('export_tahun').value = tahun;
        document.getElementById('formExport').submit();
    }
</script>
@endpush

@endsection