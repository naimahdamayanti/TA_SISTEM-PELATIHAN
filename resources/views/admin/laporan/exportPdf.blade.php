<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Pelatihan {{ $tahun }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        @page {
            size: A4 portrait;
            margin: 20mm 20mm 20mm 20mm;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10px;
            color: #1a202c;
            background: #fff;
            line-height: 1.5;
        }

        .accent-bar {
            background: #1a2744;
            height: 7px;
            margin-bottom: 0;
            width: 100%;
        }

        .header-outer {
            border-bottom: 2px solid #e84e3a;
            padding: 12px 0 10px 0;
            margin-bottom: 14px;
        }
        .header-table { width: 100%; border-collapse: collapse; }
        .header-table td { vertical-align: middle; padding: 0; }

        .logo-area img  { display: block; }
        .logo-fallback  { font-size: 15px; font-weight: bold; color: #1a2744; }
        .logo-sub       { font-size: 9px;  color: #888; margin-top: 2px; }

        .doc-title-area { text-align: right; }
        .doc-title-main {
            font-size: 13px;
            font-weight: bold;
            color: #1a2744;
            text-transform: uppercase;
            letter-spacing: 0.8px;
        }
        .doc-title-year {
            font-size: 10px;
            color: #e84e3a;
            font-weight: bold;
            margin-top: 2px;
        }
        .doc-meta   { font-size: 8.5px; color: #a0aec0; margin-top: 3px; }
        .badge-confidential {
            background: #fef3c7;
            color: #92400e;
            border: 1px solid #f59e0b;
            border-radius: 3px;
            padding: 1px 7px;
            font-size: 8px;
            font-weight: bold;
        }

        .kpi-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 14px;
        }
        .kpi-table > tr > td {
            width: 25%;
            vertical-align: top;
            padding: 0 4px;
        }
        .kpi-table > tr > td:first-child { padding-left: 0; }
        .kpi-table > tr > td:last-child  { padding-right: 0; }

        .kpi-card {
            border: 1px solid #e2e8f0;
            border-radius: 5px;
            padding: 9px 12px 8px 12px;
            background: #fff;
        }
        .kpi-card-red    { border-top: 3px solid #e84e3a; }
        .kpi-card-navy   { border-top: 3px solid #1a2744; }
        .kpi-card-green  { border-top: 3px solid #27ae60; }
        .kpi-card-amber  { border-top: 3px solid #d97706; }

        .kpi-num {
            font-size: 24px;
            font-weight: bold;
            color: #1a2744;
            line-height: 1;
        }
        .kpi-label {
            font-size: 8.5px;
            color: #4a5568;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            margin-top: 4px;
        }
        .kpi-desc { font-size: 8px; color: #a0aec0; margin-top: 2px; }

        .exec-box {
            background: #f7fafc;
            border-left: 4px solid #1a2744;
            border-radius: 0 4px 4px 0;
            padding: 11px 14px;
            margin-bottom: 14px;
        }
        .exec-title {
            font-size: 10px;
            font-weight: bold;
            color: #1a2744;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 6px;
        }
        .exec-body {
            font-size: 9.5px;
            color: #4a5568;
            line-height: 1.7;
        }
        .exec-body strong { color: #1a2744; }

        .section-header {
            margin-bottom: 8px;
            margin-top: 4px;
        }
        .section-title {
            font-size: 10.5px;
            font-weight: bold;
            color: #1a2744;
            padding-left: 10px;
            border-left: 4px solid #e84e3a;
            text-transform: uppercase;
            letter-spacing: 0.4px;
        }

        .main-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 14px;
            font-size: 9px;
        }
        .main-table thead tr {
            background: #1a2744;
        }
        .main-table thead th {
            color: #e2e8f0;
            padding: 7px 7px;
            font-size: 8.5px;
            font-weight: bold;
            text-align: left;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            border: none;
        }
        .main-table thead th.tc { text-align: center; }

        .main-table tbody tr:nth-child(even) { background: #f7fafc; }
        .main-table tbody tr:nth-child(odd)  { background: #ffffff; }

        .main-table tbody td {
            padding: 6px 7px;
            border-bottom: 1px solid #edf2f7;
            vertical-align: middle;
            color: #2d3748;
        }
        .main-table tbody td.tc { text-align: center; }

        .main-table tfoot tr { background: #edf2f7; }
        .main-table tfoot td {
            padding: 7px 7px;
            border-top: 2px solid #1a2744;
            font-size: 9px;
            font-weight: bold;
            color: #1a2744;
        }
        .main-table tfoot td.tc { text-align: center; }

        .kode {
            background: #edf2f7;
            border-radius: 3px;
            padding: 1px 5px;
            font-family: monospace;
            font-size: 9px;
            color: #4a5568;
        }

        .badge {
            display: inline-block;
            padding: 2px 7px;
            border-radius: 10px;
            font-size: 8px;
            font-weight: bold;
        }
        .badge-tersedia  { background: #d1fae5; color: #065f46; }
        .badge-penuh     { background: #fef3c7; color: #92400e; }
        .badge-selesai   { background: #dbeafe; color: #1e40af; }
        .badge-secondary { background: #e2e8f0; color: #4a5568; }

        .text-success { color: #16a34a; font-weight: bold; }
        .text-warning { color: #d97706; font-weight: bold; }
        .text-danger  { color: #dc2626; font-weight: bold; }
        .text-primary { color: #2563eb; font-weight: bold; }
        .text-muted   { color: #94a3b8; }
        .text-bold    { font-weight: bold; }

        .pbar-wrap {
            background: #e2e8f0;
            border-radius: 3px;
            height: 5px;
            width: 100%;
            margin-top: 3px;
        }
        .pbar-fill { height: 5px; border-radius: 3px; }
        .pbar-green  { background: #16a34a; }
        .pbar-yellow { background: #d97706; }
        .pbar-red    { background: #dc2626; }

        .legend-outer {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 14px;
        }
        .legend-outer > tr > td {
            vertical-align: top;
            padding: 0;
        }
        .legend-box {
            border: 1px solid #e2e8f0;
            border-radius: 4px;
            padding: 9px 11px;
            background: #f7fafc;
        }
        .legend-title {
            font-size: 9px;
            font-weight: bold;
            color: #1a2744;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            margin-bottom: 6px;
        }
        .legend-table { width: 100%; border-collapse: collapse; }
        .legend-table td { padding: 2px 0; font-size: 8.5px; color: #555; }

        .closing-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 14px;
        }
        .closing-table td {
            vertical-align: top;
            text-align: center;
            padding: 0 10px;
            font-size: 9px;
            color: #4a5568;
        }
        .closing-table td:first-child { padding-left: 0; }
        .closing-table td:last-child  { padding-right: 0; }
        .sign-line {
            border-top: 1px solid #4a5568;
            margin-top: 40px;
            padding-top: 4px;
        }
        .sign-name { font-weight: bold; color: #1a2744; font-size: 9.5px; }

        .footer-outer {
            border-top: 2px solid #1a2744;
            padding-top: 7px;
            margin-top: 10px;
        }
        .footer-table { width: 100%; border-collapse: collapse; }
        .footer-table td {
            vertical-align: middle;
            font-size: 8px;
            color: #94a3b8;
            padding: 0;
        }
        .footer-right { text-align: right; }

        .page-break    { page-break-after: always; }
        .no-break      { page-break-inside: avoid; }
    </style>
</head>
<body>

@php
    $logoPath = public_path('template/assets/img/logo/logo-expertindo.png');
    $logoSrc  = file_exists($logoPath)
        ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath))
        : null;

    $totalMenunggu   = 0;
    $totalDitolak    = 0;
    $totalLayak      = 0;
    foreach ($topPelatihan as $item) {
        $totalMenunggu += \App\Models\PendaftaranModel
            ::where('pelatihan_id', $item->id_pelatihan)
            ->where('status', 'menunggu')->count();
        $totalDitolak  += \App\Models\PendaftaranModel
            ::where('pelatihan_id', $item->id_pelatihan)
            ->where('status', 'ditolak')->count();
        $totalLayak    += \App\Models\KualifikasiSertifikasiModel
            ::whereHas('pendaftaran', fn($q) => $q->where('pelatihan_id', $item->id_pelatihan))
            ->where('memenuhi_syarat', 'lulus')   // ← string, BUKAN boolean
            ->count();
    }
    $rataKelulusan = $totalPeserta > 0
        ? round(($totalLayak / $totalPeserta) * 100, 1)
        : 0;
    $rasioSertif = $totalPeserta > 0
        ? round(($totalSertifikat / $totalPeserta) * 100, 1)
        : 0;
@endphp

<div class="accent-bar"></div>
<div class="header-outer">
    <table class="header-table">
        <tr>
            <td style="width:55%">
                @if($logoSrc)
                    <img src="{{ $logoSrc }}" style="height:52px;width:auto;" alt="Logo Expertindo">
                @else
                    <div class="logo-fallback">Expertindo Training</div>
                    <div class="logo-sub">Training &amp; Consulting</div>
                @endif
            </td>
            <td style="width:45%" class="doc-title-area">
                <div class="doc-title-main">Laporan Data Pelatihan</div>
                <div class="doc-title-year">Periode Tahun {{ $tahun }}</div>
                <div class="doc-meta">Dicetak: {{ $dicetak_pada }}</div>
                <div style="margin-top:5px">
                    <span class="badge-confidential">RAHASIA INTERNAL</span>
                </div>
            </td>
        </tr>
    </table>
</div>

<div class="exec-box">
    <div class="exec-title">&#9654; Ringkasan Eksekutif</div>
    <div class="exec-body">
        Pada tahun <strong>{{ $tahun }}</strong>, PT. Expertindo Training &amp; Consulting telah
        menyelenggarakan <strong>{{ $totalPelatihan }} program pelatihan</strong> dengan total
        <strong>{{ $totalPeserta }} peserta</strong> yang berhasil terdaftar dan diterima.
        Dari keseluruhan peserta, sebanyak <strong>{{ $totalLayak }} peserta ({{ $rataKelulusan }}%)</strong>
        dinyatakan lulus memenuhi syarat kehadiran, dan
        <strong>{{ $totalSertifikat }} sertifikat</strong> telah berhasil diterbitkan.
    </div>
    <div class="exec-body" style="margin-top:5px">
        Terdapat <strong>{{ $totalMenunggu }} pendaftaran</strong> yang masih menunggu konfirmasi
        dan <strong>{{ $totalDitolak }} pendaftaran</strong> yang ditolak dalam periode ini.
        Laporan ini disusun sebagai bahan evaluasi dan referensi pengambilan keputusan strategis.
    </div>
</div>

<div class="section-header">
    <div class="section-title">Rekap Detail Per Pelatihan</div>
</div>

<table class="main-table">
    <thead>
        <tr>
            <th style="width:22px">#</th>
            <th style="width:68px">Kode</th>
            <th>Nama Pelatihan</th>
            <th style="width:80px">Instruktur</th>
            <th class="tc" style="width:35px">Kuota</th>
            <th class="tc" style="width:42px">Diterima</th>
            <th class="tc" style="width:42px">Menunggu</th>
            <th class="tc" style="width:36px">Ditolak</th>
            <th class="tc" style="width:46px">Sertif.</th>
            <th class="tc" style="width:62px">Kelulusan</th>
            <th style="width:105px">Periode</th>
            <th class="tc" style="width:48px">Status</th>
        </tr>
    </thead>
    <tbody>
        @forelse($topPelatihan as $item)
        @php
            $diterima = $item->pendaftaran_count;

            $menunggu = \App\Models\PendaftaranModel
                ::where('pelatihan_id', $item->id_pelatihan)
                ->where('status', 'menunggu')->count();
            $ditolak  = \App\Models\PendaftaranModel
                ::where('pelatihan_id', $item->id_pelatihan)
                ->where('status', 'ditolak')->count();
            $sertif   = \App\Models\SertifikatModel
                ::whereHas('pendaftaran', fn($q) => $q->where('pelatihan_id', $item->id_pelatihan))
                ->count();
            $lulus    = \App\Models\KualifikasiSertifikasiModel
                ::whereHas('pendaftaran', fn($q) => $q->where('pelatihan_id', $item->id_pelatihan))
                ->where('memenuhi_syarat', 'lulus')  
                ->count();

            $persen = $diterima > 0 ? round(($lulus / $diterima) * 100, 1) : null;

            $kelulusanClass = match(true) {
                is_null($persen)    => 'text-muted',
                $persen >= 80       => 'text-success',
                $persen >= 50       => 'text-warning',
                default             => 'text-danger',
            };
            $pbarClass = match(true) {
                is_null($persen)    => 'pbar-red',
                $persen >= 80       => 'pbar-green',
                $persen >= 50       => 'pbar-yellow',
                default             => 'pbar-red',
            };
            $badgeClass = match($item->status ?? '') {
                'tersedia'  => 'badge-tersedia',
                'penuh'     => 'badge-penuh',
                'selesai'   => 'badge-selesai',
                default     => 'badge-secondary',
            };
        @endphp
        <tr class="no-break">
            <td class="tc text-muted">{{ $loop->iteration }}</td>
            <td><span class="kode">{{ $item->kode_pelatihan }}</span></td>
            <td><span class="text-bold">{{ $item->nama_pelatihan }}</span></td>
            <td class="text-muted" style="font-size:8.5px">{{ $item->instruktur->nama ?? '—' }}</td>
            <td class="tc">{{ $item->kuota }}</td>
            <td class="tc {{ $diterima > 0 ? 'text-success' : 'text-muted' }}">{{ $diterima }}</td>
            <td class="tc {{ $menunggu > 0 ? 'text-warning' : 'text-muted' }}">{{ $menunggu }}</td>
            <td class="tc {{ $ditolak > 0 ? 'text-danger' : 'text-muted' }}">{{ $ditolak }}</td>
            <td class="tc {{ $sertif > 0 ? 'text-primary' : 'text-muted' }}">{{ $sertif }}</td>
            <td class="tc">
                <span class="{{ $kelulusanClass }}">
                    {{ is_null($persen) ? '—' : $persen . '%' }}
                </span>
                @if(!is_null($persen))
                <div class="pbar-wrap">
                    {{-- DomPDF-safe: inline style width% pada div --}}
                    <div class="pbar-fill {{ $pbarClass }}"
                         style="width:{{ min((int)$persen, 100) }}%"></div>
                </div>
                @endif
            </td>
            <td class="text-muted" style="font-size:8.5px">
                @if($item->tgl_mulai)
                    {{ \Carbon\Carbon::parse($item->tgl_mulai)->format('d M Y') }}
                    @if($item->tgl_selesai)
                        &ndash;<br>{{ \Carbon\Carbon::parse($item->tgl_selesai)->format('d M Y') }}
                    @endif
                @else
                    <span class="text-muted">—</span>
                @endif
            </td>
            <td class="tc">
                <span class="badge {{ $badgeClass }}">{{ ucfirst($item->status ?? '—') }}</span>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="12" class="tc text-muted" style="padding:18px">
                Tidak ada data pelatihan untuk tahun {{ $tahun }}.
            </td>
        </tr>
        @endforelse
    </tbody>
    <tfoot>
        <tr>
            <td colspan="4">TOTAL KESELURUHAN</td>
            <td class="tc">—</td>
            <td class="tc">{{ $totalPeserta }}</td>
            <td class="tc">{{ $totalMenunggu }}</td>
            <td class="tc">{{ $totalDitolak }}</td>
            <td class="tc">{{ $totalSertifikat }}</td>
            <td class="tc">{{ $rataKelulusan }}%</td>
            <td colspan="2"></td>
        </tr>
    </tfoot>
</table>

<div class="section-header" style="margin-top:16px">
    <div class="section-title">Ringkasan Statistik</div>
</div>
<table class="kpi-table">
    <tr>
        <td>
            <div class="kpi-card kpi-card-red">
                <div class="kpi-num">{{ $totalPelatihan }}</div>
                <div class="kpi-label">Total Pelatihan</div>
                <div class="kpi-desc">Program tahun {{ $tahun }}</div>
            </div>
        </td>
        <td>
            <div class="kpi-card kpi-card-navy">
                <div class="kpi-num">{{ $totalPeserta }}</div>
                <div class="kpi-label">Total Peserta</div>
                <div class="kpi-desc">Diterima &amp; aktif</div>
            </div>
        </td>
        <td>
            <div class="kpi-card kpi-card-green">
                <div class="kpi-num">{{ $totalSertifikat }}</div>
                <div class="kpi-label">Sertifikat Terbit</div>
                <div class="kpi-desc">{{ $rasioSertif }}% dari peserta</div>
            </div>
        </td>
        <td>
            <div class="kpi-card kpi-card-amber">
                <div class="kpi-num">{{ $totalInstruktur }}</div>
                <div class="kpi-label">Instruktur Aktif</div>
                <div class="kpi-desc">Tenaga pengajar</div>
            </div>
        </td>
    </tr>
</table>

<table class="legend-outer">
    <tr>
        <td style="width:48%;padding-right:6px">
            <div class="legend-box">
                <div class="legend-title">Keterangan Status Pelatihan</div>
                <table class="legend-table">
                    <tr>
                        <td style="width:80px">
                            <span class="badge badge-tersedia">Tersedia</span>
                        </td>
                        <td>Pelatihan masih membuka pendaftaran</td>
                    </tr>
                    <tr>
                        <td><span class="badge badge-penuh">Penuh</span></td>
                        <td>Kuota peserta sudah terpenuhi</td>
                    </tr>
                    <tr>
                        <td><span class="badge badge-selesai">Selesai</span></td>
                        <td>Pelatihan telah selesai dilaksanakan</td>
                    </tr>
                </table>
            </div>
        </td>
        <td style="width:52%;padding-left:6px">
            <div class="legend-box">
                <div class="legend-title">Indikator Tingkat Kelulusan</div>
                <table class="legend-table">
                    <tr>
                        <td style="width:80px"><span class="text-success">&#9646; 80% ke atas</span></td>
                        <td>Kelulusan tinggi — performa baik</td>
                    </tr>
                    <tr>
                        <td><span class="text-warning">&#9646; 50% – 79%</span></td>
                        <td>Cukup — perlu perhatian lanjutan</td>
                    </tr>
                    <tr>
                        <td><span class="text-danger">&#9646; Di bawah 50%</span></td>
                        <td>Rendah — perlu evaluasi mendalam</td>
                    </tr>
                </table>
            </div>
        </td>
    </tr>
</table>

<table class="closing-table">
    <tr>
        <td style="width:35%">
            <div style="font-size:9px;color:#4a5568;text-align:left">
                Hormat kami,<br>
                <strong style="color:#1a2744">PT. Expertindo Training &amp; Consulting</strong>
            </div>
            <div class="sign-line" style="text-align:left">
                <div class="sign-name">Diterbitkan oleh Sistem SIMPERTI</div>
                <div class="text-muted">Administrator</div>
            </div>
        </td>
        <td style="width:30%"></td>
        <td style="width:35%;text-align:right">
            <div style="font-size:9px;color:#4a5568;text-align:right">
                Mengetahui &amp; Menyetujui,
            </div>
            <div class="sign-line" style="text-align:right">
                <div class="sign-name">Direktur Utama</div>
                <div class="text-muted">PT. Expertindo Training &amp; Consulting</div>
            </div>
        </td>
    </tr>
</table>

<div class="footer-outer">
    <table class="footer-table">
        <tr>
            <td>
                &copy; {{ date('Y') }} PT. Expertindo Training &amp; Consulting
                &mdash; Dokumen ini bersifat rahasia dan hanya untuk keperluan internal perusahaan.
            </td>
            <td class="footer-right">
                Laporan Tahun {{ $tahun }}&nbsp;&nbsp;|&nbsp;&nbsp;{{ $dicetak_pada }}
            </td>
        </tr>
    </table>
</div>

</body>
</html>