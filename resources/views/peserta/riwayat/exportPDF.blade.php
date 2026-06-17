<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Formulir Pendaftaran</title>
    <style>
        @page { size: A4 portrait; margin: 0; }
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 8.5pt;
            color: #333;
        }

        .doc-header {
            background: #e84e3a;
            padding: 8mm 14mm 10mm;
        }
        .dh-title {
            font-size: 12pt; font-weight: 700;
            color: #fff; letter-spacing: .05em;
            text-transform: uppercase; line-height: 1.2;
            margin-top: 4mm;
        }
        .dh-sub {
            font-size: 6.5pt;
            color: rgba(255,255,255,.8);
            margin-top: 2px;
        }
        .dh-no-lbl {
            font-size: 6.5pt; color: rgba(255,255,255,.8);
            text-transform: uppercase; letter-spacing: .06em;
            margin-bottom: 1.5mm;
        }
        .dh-no-val {
            font-size: 14pt; font-weight: 700;
            color: #fff; font-family: 'Courier New', monospace;
            letter-spacing: .04em;
        }

        .doc-body { padding: 6mm 14mm 4mm; }

        .info-box {
            background: #fff0ee; border: 1px solid #fcd0c4;
            border-left: 3px solid #e84e3a; border-radius: 2mm;
            padding: 4mm 6mm; margin-bottom: 4mm;
        }
        .ib-label {
            font-size: 6.5pt; color: #aaa;
            text-transform: uppercase; letter-spacing: .04em; margin-bottom: 1mm;
        }
        .ib-value { font-size: 9.5pt; font-weight: 700; color: #1a1a2e; }

        .section-head {
            font-size: 7.5pt; font-weight: 700; color: #e84e3a;
            text-transform: uppercase; letter-spacing: .06em;
            border-bottom: 1px solid #f0f0f0;
            padding-bottom: 1.5mm; margin: 4mm 0 3mm;
        }
        .dt-label { font-size: 7pt; color: #aaa; margin-bottom: 1mm; }
        .dt-value {
            font-size: 9pt; color: #222;
            background: #f9fafb; border: 1px solid #efefef;
            border-radius: 1.5mm; padding: 2mm 3mm; min-height: 7mm;
        }

        .badge {
            display: inline-block; padding: 1mm 4mm;
            border-radius: 999px; font-size: 8pt; font-weight: 700;
        }
        .badge-diterima { background: #dcfce7; color: #15803d; }
        .badge-menunggu { background: #fef9c3; color: #854d0e; }
        .badge-ditolak  { background: #fee2e2; color: #991b1b; }

        .doc-footer {
            position: fixed; bottom: 0; left: 0; right: 0;
            background: #f9fafb; border-top: 1px solid #eee;
            padding: 3mm 14mm; font-size: 6.5pt;
            color: #bbb; text-align: center;
        }
        .content-wrap { margin-bottom: 14mm; }
    </style>
</head>
<body>

@php
    $logoPath   = public_path('template/assets/img/logo/logo-expertindo.png');
    $logoBase64 = file_exists($logoPath)
        ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath))
        : null;
    $noPd = 'PD-' . str_pad($pendaftaran->id_pendaftaran ?? $pendaftaran->id, 3, '0', STR_PAD_LEFT);
@endphp

{{-- ══ HEADER: 2 kolom — kiri: logo+judul, kanan: no.pendaftaran ══ --}}
<div class="doc-header">
    <table style="width:100%; border-collapse:collapse; table-layout:fixed;">
        <tr>
            {{-- Kiri: Logo di atas, judul+subtitle di bawah --}}
            <td style="vertical-align:top;">
                @if($logoBase64)
                    <img src="{{ $logoBase64 }}"
                         style="height:12mm; width:auto; display:block;"
                         alt="Logo">
                @endif
                <div class="dh-title">Formulir Pendaftaran Pelatihan</div>
                <div class="dh-sub">Sistem Informasi Manajemen Pelatihan &amp; Sertifikasi</div>
            </td>

            {{-- Kanan: No. Pendaftaran, rata kanan, vertikal tengah --}}
            <td style="width:46mm; vertical-align:middle; text-align:right;">
                <div class="dh-no-lbl">No. Pendaftaran</div>
                <div class="dh-no-val">{{ $noPd }}</div>
            </td>
        </tr>
    </table>
</div>

{{-- ══ CONTENT ══ --}}
<div class="content-wrap">
<div class="doc-body">

    <div class="section-head">Informasi Pelatihan</div>
    <div class="info-box">
        <table style="width:100%; border-collapse:collapse; table-layout:fixed;">
            <tr>
                <td style="width:50%; padding:0 4mm 3mm 0; vertical-align:top;">
                    <div class="ib-label">Nama Pelatihan</div>
                    <div class="ib-value">{{ $pendaftaran->pelatihan?->nama_pelatihan ?? '-' }}</div>
                </td>
                <td style="width:50%; padding:0 0 3mm 0; vertical-align:top;">
                    <div class="ib-label">Kode</div>
                    <div class="ib-value">{{ $pendaftaran->pelatihan?->kode_pelatihan ?? '-' }}</div>
                </td>
            </tr>
            <tr>
                <td style="width:50%; padding:0 4mm 0 0; vertical-align:top;">
                    <div class="ib-label">Instruktur</div>
                    <div class="ib-value">
                        {{ $pendaftaran->pelatihan?->instruktur?->nama_lengkap
                           ?? $pendaftaran->pelatihan?->instruktur?->nama ?? '-' }}
                    </div>
                </td>
                <td style="width:50%; vertical-align:top;">
                    <div class="ib-label">Periode</div>
                    <div class="ib-value">
                        @if($pendaftaran->pelatihan?->tgl_mulai && $pendaftaran->pelatihan?->tgl_selesai)
                            {{ \Carbon\Carbon::parse($pendaftaran->pelatihan->tgl_mulai)->translatedFormat('j M Y') }}
                            &ndash;
                            {{ \Carbon\Carbon::parse($pendaftaran->pelatihan->tgl_selesai)->translatedFormat('j M Y') }}
                        @else - @endif
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <div class="section-head">Data Diri Peserta</div>
    <table style="width:100%; border-collapse:collapse; table-layout:fixed; margin-bottom:3mm;">
        <tr>
            <td style="width:50%; padding:0 3mm 3mm 0; vertical-align:top;">
                <div class="dt-label">Nama Lengkap</div>
                <div class="dt-value">{{ trim(($pendaftaran->first_name ?? '') . ' ' . ($pendaftaran->last_name ?? '')) ?: '-' }}</div>
            </td>
            <td style="width:50%; padding:0 0 3mm 3mm; vertical-align:top;">
                <div class="dt-label">Email</div>
                <div class="dt-value">{{ $pendaftaran->email ?? '-' }}</div>
            </td>
        </tr>
        <tr>
            <td style="width:50%; padding:0 3mm 3mm 0; vertical-align:top;">
                <div class="dt-label">No. HP</div>
                <div class="dt-value">{{ $pendaftaran->no_hp ?? '-' }}</div>
            </td>
            <td style="width:50%; padding:0 0 3mm 3mm; vertical-align:top;">
                <div class="dt-label">Tanggal Daftar</div>
                <div class="dt-value">
                    {{ $pendaftaran->tgl_daftar
                        ? \Carbon\Carbon::parse($pendaftaran->tgl_daftar)->translatedFormat('j M Y') : '-' }}
                </div>
            </td>
        </tr>
        <tr>
            <td colspan="2" style="padding:0; vertical-align:top;">
                <div class="dt-label">Alamat</div>
                <div class="dt-value" style="min-height:10mm;">{{ $pendaftaran->alamat ?? '-' }}</div>
            </td>
        </tr>
    </table>

    <div class="section-head">Data Pekerjaan &amp; Perusahaan</div>
    <table style="width:100%; border-collapse:collapse; table-layout:fixed; margin-bottom:3mm;">
        <tr>
            <td style="width:50%; padding:0 3mm 3mm 0; vertical-align:top;">
                <div class="dt-label">Pekerjaan / Jabatan</div>
                <div class="dt-value">{{ $pendaftaran->pekerjaan ?? '-' }}</div>
            </td>
            <td style="width:50%; padding:0 0 3mm 3mm; vertical-align:top;">
                <div class="dt-label">Nama Perusahaan</div>
                <div class="dt-value">{{ $pendaftaran->perusahaan ?? '-' }}</div>
            </td>
        </tr>
        <tr>
            <td style="width:50%; padding:0 3mm 0 0; vertical-align:top;">
                <div class="dt-label">No. Telp Perusahaan</div>
                <div class="dt-value">{{ $pendaftaran->tlp_perusahaan ?? '-' }}</div>
            </td>
            <td style="width:50%; padding:0 0 0 3mm;"></td>
        </tr>
    </table>

    <div class="section-head">Pesan / Keterangan</div>
    <div class="dt-value" style="min-height:12mm; margin-bottom:5mm;">
        {{ $pendaftaran->pesan ?? '-' }}
    </div>

    <hr style="border:none; border-top:1px solid #f0f0f0; margin:4mm 0;">

    <table style="width:100%; border-collapse:collapse; table-layout:fixed;">
        <tr>
            <td style="width:50%; vertical-align:middle;">
                <div style="font-size:7.5pt; color:#888; font-weight:600; margin-bottom:2mm;">Status Pendaftaran</div>
                @php $st = $pendaftaran->status ?? 'menunggu'; @endphp
                <span class="badge badge-{{ $st }}">{{ ucfirst($st) }}</span>
            </td>
            <td style="width:50%; vertical-align:bottom; text-align:right;">
                <div style="display:inline-block; border:1px dashed #ccc;
                            border-radius:2mm; padding:2mm 8mm; font-size:7.5pt;
                            color:#aaa; font-style:italic; text-align:center; min-width:46mm;">
                    <div style="height:9mm;"></div>
                    Tanda Tangan Peserta
                </div>
            </td>
        </tr>
    </table>

</div>
</div>

<div class="doc-footer">
    Sistem Informasi Manajemen Pelatihan &amp; Sertifikasi
    &nbsp;&bull;&nbsp;
    Dicetak: {{ $tgl_cetak->translatedFormat('j F Y') }} pukul {{ $tgl_cetak->format('H:i') }} WIB
</div>

</body>
</html>