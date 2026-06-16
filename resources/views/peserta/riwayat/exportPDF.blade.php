<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Formulir Pendaftaran – {{ 'PD-' . str_pad($pendaftaran->id_pendaftaran ?? $pendaftaran->id, 3, '0', STR_PAD_LEFT) }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 9pt;
            color: #333;
            background: #fff;
        }

        .page { width: 190mm; padding: 0; }

        /* ─── HEADER MERAH ─── */
        .doc-header {
            background: #e84e3a;
            padding: 12mm 14mm 10mm;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .dh-left { display: flex; align-items: center; gap: 10px; }
        .dh-icon {
            width: 12mm; height: 12mm;
            background: rgba(255,255,255,.2);
            border-radius: 2mm;
            display: flex; align-items: center; justify-content: center;
            font-size: 6mm; color: #fff; font-weight: 900;
            line-height: 1; text-align: center;
        }
        .dh-title {
            font-size: 11pt; font-weight: 700;
            color: #fff; letter-spacing: .04em;
            text-transform: uppercase; line-height: 1.2;
        }
        .dh-sub { font-size: 6.5pt; color: rgba(255,255,255,.7); margin-top: 2px; }

        .dh-right { text-align: right; }
        .dh-no-lbl { font-size: 7pt; color: rgba(255,255,255,.6); text-transform: uppercase; letter-spacing: .05em; }
        .dh-no-val { font-size: 12pt; font-weight: 700; color: #fff; font-family: 'Courier New', monospace; }

        /* ─── BODY ─── */
        .doc-body { padding: 8mm 14mm 10mm; }

        /* Info pelatihan box */
        .info-box {
            background: #fff0ee;
            border: 1px solid #fcd0c4;
            border-left: 3px solid #e84e3a;
            border-radius: 2mm;
            padding: 5mm 7mm;
            margin-bottom: 7mm;
        }
        .info-box-grid {
            display: table;
            width: 100%;
        }
        .info-box-row { display: table-row; }
        .info-box-cell {
            display: table-cell;
            width: 50%;
            padding: 2mm 3mm 2mm 0;
            vertical-align: top;
        }
        .ib-label { font-size: 7pt; color: #aaa; text-transform: uppercase; letter-spacing: .04em; margin-bottom: 1mm; }
        .ib-value { font-size: 10pt; font-weight: 700; color: #1a1a2e; }

        /* Section header */
        .section-head {
            font-size: 8pt; font-weight: 700;
            color: #e84e3a;
            text-transform: uppercase; letter-spacing: .06em;
            border-bottom: 1px solid #f0f0f0;
            padding-bottom: 2mm;
            margin: 6mm 0 4mm;
        }

        /* Data grid — pakai table agar DomPDF render rapi */
        .data-table { width: 100%; border-collapse: collapse; margin-bottom: 3mm; }
        .data-table td { padding: 2mm 3mm; vertical-align: top; width: 50%; }
        .dt-label { font-size: 7.5pt; color: #aaa; margin-bottom: 1.5mm; }
        .dt-value {
            font-size: 9.5pt; color: #222;
            background: #f9fafb;
            border: 1px solid #efefef;
            border-radius: 1.5mm;
            padding: 2mm 3mm;
            min-height: 8mm;
        }

        /* Full-width field */
        .data-table-full { width: 100%; border-collapse: collapse; margin-bottom: 3mm; }
        .data-table-full td { padding: 2mm 3mm; }

        /* Status & TTD baris */
        .status-row {
            display: table;
            width: 100%;
            margin-top: 6mm;
            padding-top: 5mm;
            border-top: 1px solid #f0f0f0;
        }
        .sr-left  { display: table-cell; vertical-align: middle; width: 50%; }
        .sr-right {
            display: table-cell; vertical-align: middle;
            width: 50%; text-align: right;
        }
        .sr-lbl { font-size: 8pt; color: #888; font-weight: 600; margin-bottom: 2mm; }

        /* Badge status */
        .badge {
            display: inline-block;
            padding: 1.5mm 4mm;
            border-radius: 999px;
            font-size: 8pt; font-weight: 700;
        }
        .badge-diterima { background: #dcfce7; color: #15803d; }
        .badge-menunggu { background: #fef9c3; color: #854d0e; }
        .badge-ditolak  { background: #fee2e2; color: #991b1b; }

        /* TTD box */
        .ttd-box {
            display: inline-block;
            border: 1px dashed #ddd;
            border-radius: 2mm;
            padding: 3mm 8mm;
            font-size: 8pt;
            color: #aaa;
            font-style: italic;
        }

        /* ─── FOOTER ─── */
        .doc-footer {
            background: #f9fafb;
            border-top: 1px solid #eee;
            padding: 4mm 14mm;
            font-size: 7pt;
            color: #bbb;
            text-align: center;
        }
    </style>
</head>
<body>
<div class="page">

    {{-- ── HEADER MERAH ── --}}
    <div class="doc-header">
        @php
            $logoPath = public_path('template/assets/img/logo/logo-expertindo.png');
            $logoBase64 = file_exists($logoPath)
                ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath))
                : null;
        @endphp

        <div class="dh-left">
            <div class="sidebar-brand">
                @if($logoBase64)
                    <img src="{{ $logoBase64 }}"
                        style="height:14mm; width:auto;" alt="logo">
                @endif
            </div>
            <div>
                <div class="dh-title">Formulir Pendaftaran Pelatihan</div>
                <div class="dh-sub">Sistem Informasi Manajemen Pelatihan &amp; Sertifikasi</div>
            </div>
        </div>
        <div class="dh-right">
            <div class="dh-no-lbl">No. Pendaftaran</div>
            <div class="dh-no-val">
                PD-{{ str_pad($pendaftaran->id_pendaftaran ?? $pendaftaran->id, 3, '0', STR_PAD_LEFT) }}
            </div>
        </div>
    </div>

    {{-- ── BODY ── --}}
    <div class="doc-body">

        {{-- Info Pelatihan --}}
        <div class="section-head">Informasi Pelatihan</div>
        <div class="info-box">
            <div class="info-box-grid">
                <div class="info-box-row">
                    <div class="info-box-cell">
                        <div class="ib-label">Nama Pelatihan</div>
                        <div class="ib-value">{{ $pendaftaran->pelatihan?->nama_pelatihan ?? '-' }}</div>
                    </div>
                    <div class="info-box-cell">
                        <div class="ib-label">Kode</div>
                        <div class="ib-value">{{ $pendaftaran->pelatihan?->kode_pelatihan ?? '-' }}</div>
                    </div>
                </div>
                <div class="info-box-row">
                    <div class="info-box-cell">
                        <div class="ib-label">Instruktur</div>
                        <div class="ib-value">
                            {{ $pendaftaran->pelatihan?->instruktur?->nama_lengkap
                               ?? $pendaftaran->pelatihan?->instruktur?->nama
                               ?? '-' }}
                        </div>
                    </div>
                    <div class="info-box-cell">
                        <div class="ib-label">Periode</div>
                        <div class="ib-value">
                            @if($pendaftaran->pelatihan?->tgl_mulai && $pendaftaran->pelatihan?->tgl_selesai)
                                {{ \Carbon\Carbon::parse($pendaftaran->pelatihan->tgl_mulai)->translatedFormat('j M Y') }}
                                –
                                {{ \Carbon\Carbon::parse($pendaftaran->pelatihan->tgl_selesai)->translatedFormat('j M Y') }}
                            @else
                                -
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Data Diri Peserta --}}
        <div class="section-head">&#9998; Data Diri Peserta</div>

        <table class="data-table">
            <tr>
                <td>
                    <div class="dt-label">Nama Lengkap</div>
                    <div class="dt-value">
                        {{ trim($pendaftaran->first_name . ' ' . $pendaftaran->last_name) ?: '-' }}
                    </div>
                </td>
                <td>
                    <div class="dt-label">Email</div>
                    <div class="dt-value">{{ $pendaftaran->email ?? '-' }}</div>
                </td>
            </tr>
            <tr>
                <td>
                    <div class="dt-label">No. HP</div>
                    <div class="dt-value">{{ $pendaftaran->no_hp ?? '-' }}</div>
                </td>
                <td>
                    <div class="dt-label">Tanggal Daftar</div>
                    <div class="dt-value">
                        {{ $pendaftaran->tgl_daftar
                            ? \Carbon\Carbon::parse($pendaftaran->tgl_daftar)->translatedFormat('j M Y')
                            : '-' }}
                    </div>
                </td>
            </tr>
        </table>

        <table class="data-table-full">
            <tr>
                <td>
                    <div class="dt-label">Alamat</div>
                    <div class="dt-value" style="min-height:14mm">{{ $pendaftaran->alamat ?? '-' }}</div>
                </td>
            </tr>
        </table>

        {{-- Data Pekerjaan --}}
        <div class="section-head">&#128188; Data Pekerjaan &amp; Perusahaan</div>

        <table class="data-table">
            <tr>
                <td>
                    <div class="dt-label">Pekerjaan / Jabatan</div>
                    <div class="dt-value">{{ $pendaftaran->pekerjaan ?? '-' }}</div>
                </td>
                <td>
                    <div class="dt-label">Nama Perusahaan</div>
                    <div class="dt-value">{{ $pendaftaran->perusahaan ?? '-' }}</div>
                </td>
            </tr>
        </table>

        <table class="data-table-full">
            <tr>
                <td style="width:50%">
                    <div class="dt-label">No. Telp Perusahaan</div>
                    <div class="dt-value">{{ $pendaftaran->tlp_perusahaan ?? '-' }}</div>
                </td>
                <td style="width:50%"></td>
            </tr>
        </table>

        {{-- Pesan --}}
        <div class="section-head">&#128172; Pesan / Keterangan</div>
        <table class="data-table-full">
            <tr>
                <td>
                    <div class="dt-value" style="min-height:16mm">{{ $pendaftaran->pesan ?? '-' }}</div>
                </td>
            </tr>
        </table>

        {{-- Status & TTD --}}
        <div class="status-row">
            <div class="sr-left">
                <div class="sr-lbl">Status Pendaftaran</div>
                @php $st = $pendaftaran->status; @endphp
                <span class="badge badge-{{ $st }}">{{ ucfirst($st) }}</span>
            </div>
            <div class="sr-right">
                <div class="ttd-box">Tanda Tangan Peserta</div>
            </div>
        </div>

    </div>{{-- /doc-body --}}

    {{-- ── FOOTER ── --}}
    <div class="doc-footer">
        Sistem Informasi Manajemen Pelatihan &amp; Sertifikasi
        &nbsp;|&nbsp;
        Dicetak: {{ $tgl_cetak->translatedFormat('j F Y') }} pukul {{ $tgl_cetak->format('H:i') }} WIB
    </div>

</div>
</body>
</html>