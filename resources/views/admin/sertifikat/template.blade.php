<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <style>
        @page {
            size: A4 landscape;
            margin: 0;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        html, body { width: 297mm; height: 210mm; }
        body { font-family: 'DejaVu Sans', Arial, sans-serif; }

        .wrap {
            position: relative;
            width: 297mm;
            height: 210mm;
            overflow: hidden;
        }

        .bg-template {
            position: absolute;
            top: 0; left: 0;
            width: 297mm;
            height: 210mm;
        }

        .overlay {
            position: absolute;
            top: 0; left: 0;
            width: 100%; height: 100%;
        }

        .nama-peserta {
            font-family: 'DejaVu Serif', Georgia, serif;
            font-size: 26pt;
            font-weight: bold;
            font-style: italic;
            color: #1a1a2e;
            letter-spacing: 0.5pt;
            line-height: 1.1;
        }

        .nama-pelatihan {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 11pt;
            font-weight: bold;
            color: #111111;
            text-transform: uppercase;
            letter-spacing: 0.4pt;
            line-height: 1.3;
        }

        .nomor-sertifikat {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 9pt;
            color: #444444;
            letter-spacing: 0.2pt;
        }

        .tgl-terbit {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 9pt;
            color: #555555;
        }

        .diterbitkan {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 10pt;
            font-weight: bold;
            color: #111111;
            text-decoration: underline;
        }

        .kode {
            font-family: 'DejaVu Sans Mono', 'Courier New', monospace;
            font-size: 8pt;
            color: #888888;
            letter-spacing: 0.6pt;
        }
    </style>
</head>
<body>
@php
    
    $boxWidth = [
        'nama_peserta'     => 200,
        'nama_pelatihan'   => 180,
        'nomor_sertifikat' => 140,
        'tgl_terbit'       => 90,
        'diterbitkan_oleh' => 90,
        'kode'             => 120,
    ];

    $styleFor = function (string $field) use ($posisi, $boxWidth) {
        $p   = $posisi[$field];
        $xMm = $p['x'] / 100 * 297;
        $yMm = $p['y'] / 100 * 210;
        $w   = $boxWidth[$field];

        $style = "position:absolute; top:{$yMm}mm; width:{$w}mm; text-align:{$p['align']};";

        if ($p['align'] === 'center') {
            $style .= ' left:' . ($xMm - $w / 2) . 'mm;';
        } elseif ($p['align'] === 'right') {
            $style .= ' left:' . ($xMm - $w) . 'mm;';
        } else {
            $style .= " left:{$xMm}mm;";
        }

        return $style;
    };

    $ttdStyle = null;
    if (isset($posisi['tanda_tangan'])) {
        $p     = $posisi['tanda_tangan'];
        $xMm   = $p['x'] / 100 * 297;
        $yMm   = $p['y'] / 100 * 210;
        $w     = 45; // lebar gambar tanda tangan dalam mm
        $left  = $xMm - $w / 2;
        $ttdStyle = "position:absolute; top:{$yMm}mm; left:{$left}mm; width:{$w}mm;";
    }

    $namaPeserta = trim(($pendaftaran->first_name ?? '') . ' ' . ($pendaftaran->last_name ?? ''));
    if (!$namaPeserta) {
        $namaPeserta = $peserta->nama ?? '-';
    }
@endphp

<div class="wrap">

    @if($templateBase64)
        <div class="bg-template">
            <img src="{{ $templateBase64 }}"
                 alt="template"
                 style="width:297mm; height:210mm; display:block;">
        </div>
    @else
        <div class="bg-template" style="background:#ffffff; border:4px solid #e84e3a;"></div>
    @endif

    <div class="overlay">

        <div class="nama-peserta" style="{{ $styleFor('nama_peserta') }}">
            {{ $namaPeserta }}
        </div>

        <div class="nama-pelatihan" style="{{ $styleFor('nama_pelatihan') }}">
            {{ $pelatihan->nama_pelatihan ?? '-' }}
        </div>

        <div class="nomor-sertifikat" style="{{ $styleFor('nomor_sertifikat') }}">
            No: {{ $nomor_sertifikat }}
        </div>

        <div class="tgl-terbit" style="{{ $styleFor('tgl_terbit') }}">
            {{ \Carbon\Carbon::parse($tgl_terbit)->translatedFormat('j F Y') }}
        </div>

        <div class="diterbitkan" style="{{ $styleFor('diterbitkan_oleh') }}">
            {{ $diterbitkan_oleh }}
        </div>

        <div class="kode" style="{{ $styleFor('kode') }}">
            {{ $kode }}
        </div>

        @if(!empty($ttdBase64) && $ttdStyle)
        <div style="{{ $ttdStyle }}">
            <img src="{{ $ttdBase64 }}"
                 alt="tanda tangan"
                 style="width:100%; height:auto; max-height:20mm; display:block;">
        </div>
        @endif

    </div>

</div>
</body>
</html>