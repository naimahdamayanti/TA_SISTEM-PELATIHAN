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

        /* Template gambar jadi background penuh */
        .bg-template {
            position: absolute;
            top: 0; left: 0;
            width: 297mm;
            height: 210mm;
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }

        .overlay {
            position: absolute;
            top: 0; left: 0;
            width: 100%; height: 100%;
        }

        .nama-peserta {
            font-size: 22pt;
            font-weight: 700;
            color: #1a1a2e;
            font-family: Georgia, serif;
        }
        .nama-pelatihan {
            font-size: 12pt;
            color: #333;
        }
        .kode {
            font-size: 9pt;
            color: #888;
            font-family: 'Courier New', monospace;
        }
        .tgl-terbit {
            font-size: 9pt;
            color: #555;
        }
        .diterbitkan {
            font-size: 9pt;
            color: #555;
        }
        .persen-hadir {
            font-size: 10pt;
            color: #555;
        }
    </style>
</head>
<body>
@php
    // Lebar "kotak" teks per field (mm) — untuk hitung left/right dari titik klik
    $boxWidth = [
        'nama_peserta'     => 200,
        'nama_pelatihan'   => 150,
        'nomor_sertifikat' => 120,
        'tgl_terbit'       => 80,
        'diterbitkan_oleh' => 80,
        'kode'             => 100,
    ];

    $styleFor = function (string $field) use ($posisi, $boxWidth) {
        $p = $posisi[$field];
        $xMm = $p['x'] / 100 * 297;
        $yMm = $p['y'] / 100 * 210;
        $w   = $boxWidth[$field];

        $style = "position:absolute; top:{$yMm}mm; width:{$w}mm; text-align:{$p['align']};";

        if ($p['align'] === 'center') {
            $style .= " left:" . ($xMm - $w / 2) . "mm;";
        } elseif ($p['align'] === 'right') {
            $style .= " left:" . ($xMm - $w) . "mm;";
        } else {
            $style .= " left:{$xMm}mm;";
        }

        return $style;
    };

    $ttdStyle = null;
    if (isset($posisi['tanda_tangan'])) {
        $p    = $posisi['tanda_tangan'];
        $xMm  = $p['x'] / 100 * 297;
        $yMm  = $p['y'] / 100 * 210;
        $w    = 45; // lebar tanda tangan dalam mm, sesuaikan
        $left = $xMm - $w / 2; // selalu center
        $ttdStyle = "position:absolute; top:{$yMm}mm; left:{$left}mm; width:{$w}mm;";
    }
@endphp
<div class="wrap">

    @if($templateBase64)
        <div class="bg-template">
            <img src="{{ $templateBase64 }}" alt="template" style="width:297mm;height:210mm;display:block;">
        </div>
    @else
        <div class="bg-template" style="background:#fff;border:4px solid #e84e3a;"></div>
    @endif

    {{-- Overlay data peserta --}}
    <div class="overlay">
        <div class="nama-peserta" style="{{ $styleFor('nama_peserta') }}">
            {{ trim(($pendaftaran->first_name ?? '') . ' ' . ($pendaftaran->last_name ?? '')) ?: ($peserta->nama_lengkap ?? '-') }}
        </div>
        <div class="nama-pelatihan" style="{{ $styleFor('nama_pelatihan') }}">{{ $pelatihan->nama_pelatihan ?? '-' }}</div>
        <div style="{{ $styleFor('nomor_sertifikat') }} font-size:10pt; color:#555;">{{ $nomor_sertifikat }}</div>
        <div class="tgl-terbit" style="{{ $styleFor('tgl_terbit') }}">
            {{ \Carbon\Carbon::parse($tgl_terbit)->translatedFormat('j F Y') }}
        </div>
        <div class="diterbitkan" style="{{ $styleFor('diterbitkan_oleh') }}">{{ $diterbitkan_oleh }}</div>
        <div class="kode" style="{{ $styleFor('kode') }}">{{ $kode }}</div>
        @if(!empty($ttdBase64) && $ttdStyle)
        <div style="{{ $ttdStyle }}">
            <img src="{{ $ttdBase64 }}"
                alt="ttd"
                style="width:100%; height:auto; max-height:20mm; display:block;">
        </div>
        @endif
    </div>

</div>
</body>
</html>