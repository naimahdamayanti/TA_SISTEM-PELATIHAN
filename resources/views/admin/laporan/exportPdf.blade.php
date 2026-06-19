<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Pelatihan {{ $tahun }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11px;
            color: #222;
            background: #fff;
        }

        /* ── Header ── */
        .header {
            border-bottom: 3px solid #e84e3a;
            padding-bottom: 12px;
            margin-bottom: 18px;
        }
        .header-top {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }
        .brand { font-size: 18px; font-weight: bold; color: #e84e3a; }
        .brand-sub { font-size: 10px; color: #888; }
        .doc-title { text-align: right; }
        .doc-title h1 { font-size: 15px; font-weight: bold; color: #222; }
        .doc-title p { font-size: 10px; color: #888; margin-top: 2px; }

        /* ── Stat Cards ── */
        .stats {
            display: flex;
            gap: 10px;
            margin-bottom: 18px;
        }
        .stat-box {
            flex: 1;
            border: 1px solid #eee;
            border-radius: 6px;
            padding: 10px 12px;
            text-align: center;
            background: #fafafa;
        }
        .stat-box .num {
            font-size: 22px;
            font-weight: bold;
            color: #e84e3a;
            line-height: 1.1;
        }
        .stat-box .lbl {
            font-size: 9px;
            color: #888;
            margin-top: 2px;
        }

        /* ── Section Title ── */
        .section-title {
            font-size: 12px;
            font-weight: bold;
            color: #e84e3a;
            border-left: 3px solid #e84e3a;
            padding-left: 8px;
            margin-bottom: 10px;
        }

        /* ── Table ── */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        thead tr {
            background: #e84e3a;
            color: #fff;
        }
        thead th {
            padding: 7px 10px;
            font-size: 10px;
            font-weight: bold;
            text-align: left;
        }
        thead th.center { text-align: center; }
        tbody tr:nth-child(even) { background: #fef9f8; }
        tbody tr:nth-child(odd)  { background: #fff; }
        tbody td {
            padding: 6px 10px;
            font-size: 10px;
            border-bottom: 1px solid #f0f0f0;
            vertical-align: middle;
        }
        tbody td.center { text-align: center; }

        .badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 9px;
            font-weight: bold;
        }
        .badge-success  { background: #d4edda; color: #155724; }
        .badge-warning  { background: #fff3cd; color: #856404; }
        .badge-secondary{ background: #e2e3e5; color: #383d41; }

        .text-success { color: #27ae60; font-weight: bold; }
        .text-warning { color: #f39c12; font-weight: bold; }
        .text-danger  { color: #e74c3c; font-weight: bold; }
        .text-primary { color: #3498db; font-weight: bold; }
        .text-muted   { color: #888; }

        .kode {
            background: #f0f0f0;
            border-radius: 4px;
            padding: 1px 6px;
            font-family: monospace;
            font-size: 10px;
        }

        /* ── Footer ── */
        .footer {
            border-top: 1px solid #eee;
            padding-top: 8px;
            margin-top: 10px;
            display: flex;
            justify-content: space-between;
            font-size: 9px;
            color: #aaa;
        }

        /* ── Page break ── */
        .page-break { page-break-after: always; }
    </style>
</head>
<body>

@php
    $logoPath = public_path('template/assets/img/logo/logo-expertindo.png');
    $logoSrc  = file_exists($logoPath)
        ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath))
        : null;
@endphp

    {{-- ── HEADER ── --}}
    <div class="header">
        <div class="header-top">
            <div>
                @if($logoSrc)
                    <img src="{{ $logoSrc }}" style="height:60px; width:auto;" alt="Logo">
                @endif
            </div>
            <div class="doc-title">
                <h1>Laporan Data Pelatihan {{ $tahun }}</h1>
                <p>Dicetak pada: {{ $dicetak_pada }}</p>
            </div>
        </div>
    </div>

    {{-- ── STATISTIK ── --}}
    <div class="stats">
        <div class="stat-box">
            <div class="num">{{ $totalPelatihan }}</div>
            <div class="lbl">Total Pelatihan</div>
        </div>
        <div class="stat-box">
            <div class="num">{{ $totalPeserta }}</div>
            <div class="lbl">Total Peserta</div>
        </div>
        <div class="stat-box">
            <div class="num">{{ $totalInstruktur }}</div>
            <div class="lbl">Total Instruktur</div>
        </div>
        <div class="stat-box">
            <div class="num">{{ $totalSertifikat }}</div>
            <div class="lbl">Sertifikat Terbit</div>
        </div>
    </div>

    {{-- ── TABEL PELATIHAN ── --}}
    <div class="section-title">Rekap Pelatihan</div>
    <table>
        <thead>
            <tr>
                <th style="width:80px">Kode</th>
                <th>Nama Pelatihan</th>
                <th>Instruktur</th>
                <th class="center" style="width:45px">Kuota</th>
                <th class="center" style="width:55px">Diterima</th>
                <th class="center" style="width:55px">Menunggu</th>
                <th class="center" style="width:50px">Ditolak</th>
                <th class="center" style="width:55px">Sertifikat</th>
                <th class="center" style="width:60px">Kelulusan</th>
                <th style="width:150px">Periode</th>
                <th class="center" style="width:65px">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($topPelatihan as $item)
            @php
                $diterima = $item->pendaftaran_count;
                $menunggu = \App\Models\PendaftaranModel::where('pelatihan_id', $item->id_pelatihan)->where('status','menunggu')->count();
                $ditolak  = \App\Models\PendaftaranModel::where('pelatihan_id', $item->id_pelatihan)->where('status','ditolak')->count();
                $sertif   = \App\Models\SertifikatModel::whereHas('pendaftaran', fn($q) => $q->where('pelatihan_id', $item->id_pelatihan))->count();

                $totalLulus = \App\Models\KualifikasiSertifikasiModel::whereHas('pendaftaran', fn($q) =>
                    $q->where('pelatihan_id', $item->id_pelatihan)
                )->where('memenuhi_syarat', true)->count();

                $persenKelulusan = $diterima > 0 ? round(($totalLulus / $diterima) * 100, 1) : null;

                $kelulusanClass = match(true) {
                    is_null($persenKelulusan) => 'text-muted',
                    $persenKelulusan >= 80    => 'text-success',
                    $persenKelulusan >= 50    => 'text-warning',
                    default                   => 'text-danger',
                };
            @endphp
            <tr>
                <td><span class="kode">{{ $item->kode_pelatihan }}</span></td>
                <td><strong>{{ $item->nama_pelatihan }}</strong></td>
                <td class="text-muted">{{ $item->instruktur->nama ?? '-' }}</td>
                <td class="center">{{ $item->kuota }}</td>
                <td class="center {{ $diterima > 0 ? 'text-success' : 'text-muted' }}">{{ $diterima }}</td>
                <td class="center {{ $menunggu > 0 ? 'text-warning' : 'text-muted' }}">{{ $menunggu }}</td>
                <td class="center {{ $ditolak > 0 ? 'text-danger' : 'text-muted' }}">{{ $ditolak }}</td>
                <td class="center {{ $sertif > 0 ? 'text-primary' : 'text-muted' }}">{{ $sertif }}</td>
                <td class="center {{ $kelulusanClass }}">
                    {{ is_null($persenKelulusan) ? '—' : $persenKelulusan . '%' }}
                </td>
                <td class="text-muted">
                    @if($item->tgl_mulai)
                        {{ \Carbon\Carbon::parse($item->tgl_mulai)->format('d M Y') }}
                        @if($item->tgl_selesai)
                            – {{ \Carbon\Carbon::parse($item->tgl_selesai)->format('d M Y') }}
                        @endif
                    @else —
                    @endif
                </td>
                <td class="center">
                    @php $sc = match($item->status) { 'tersedia'=>'success','penuh'=>'warning',default=>'secondary' }; @endphp
                    <span class="badge badge-{{ $sc }}">{{ ucfirst($item->status) }}</span>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="11" class="center text-muted">Tidak ada data.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    {{-- ── FOOTER ── --}}
    <div class="footer">
        <span>Copyright &copy; {{ date('Y') }} &mdash; Expertindo Training &amp; Consulting</span>
        <span>Laporan Tahun {{ $tahun }}</span>
    </div>

</body>
</html>