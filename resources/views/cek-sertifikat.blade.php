<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Sertifikat — SIMPERTI</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --orange:     #E8541A;
            --orange-dk:  #C94210;
            --orange-lt:  #F97040;
            --orange-bg:  #FEF3EE;
            --green:      #16A34A;
            --green-bg:   #F0FDF4;
            --green-bd:   #BBF7D0;
            --red:        #DC2626;
            --red-bg:     #FEF2F2;
            --red-bd:     #FECACA;
            --gray-50:    #F9FAFB;
            --gray-100:   #F3F4F6;
            --gray-200:   #E5E7EB;
            --gray-400:   #9CA3AF;
            --gray-500:   #6B7280;
            --gray-700:   #374151;
            --gray-900:   #111827;
            --white:      #FFFFFF;
            --shadow-sm:  0 1px 3px rgba(0,0,0,.08), 0 1px 2px rgba(0,0,0,.05);
            --shadow-md:  0 4px 16px rgba(0,0,0,.10), 0 2px 6px rgba(0,0,0,.06);
            --shadow-lg:  0 12px 40px rgba(0,0,0,.12), 0 4px 12px rgba(0,0,0,.07);
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--gray-100);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px 16px;
        }

        /* ── Card Wrapper ── */
        .card {
            width: 100%;
            max-width: 560px;
            background: var(--white);
            border-radius: 20px;
            box-shadow: var(--shadow-lg);
            overflow: hidden;
            animation: slideUp .45s cubic-bezier(.22,.68,0,1.2) both;
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(28px) scale(.97); }
            to   { opacity: 1; transform: translateY(0) scale(1); }
        }

        /* ── Header oranye ── */
        .card-header {
            background: linear-gradient(135deg, var(--orange) 0%, var(--orange-lt) 100%);
            padding: 40px 40px 36px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        /* Lingkaran dekoratif di background header */
        .card-header::before,
        .card-header::after {
            content: '';
            position: absolute;
            border-radius: 50%;
            background: rgba(255,255,255,.08);
        }
        .card-header::before {
            width: 200px; height: 200px;
            top: -80px; right: -60px;
        }
        .card-header::after {
            width: 140px; height: 140px;
            bottom: -60px; left: -40px;
        }

        .header-icon {
            width: 60px;
            height: 60px;
            background: rgba(255,255,255,.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 18px;
            position: relative;
            z-index: 1;
        }

        .header-icon svg {
            width: 30px;
            height: 30px;
            fill: var(--white);
        }

        .card-header h1 {
            font-size: 1.55rem;
            font-weight: 800;
            color: var(--white);
            margin-bottom: 8px;
            position: relative;
            z-index: 1;
            letter-spacing: -.3px;
        }

        .card-header p {
            font-size: .875rem;
            color: rgba(255,255,255,.85);
            line-height: 1.5;
            position: relative;
            z-index: 1;
        }

        /* ── Body form ── */
        .card-body {
            padding: 36px 40px 32px;
        }

        /* Alert sukses / error */
        .alert {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            padding: 14px 16px;
            border-radius: 10px;
            font-size: .875rem;
            line-height: 1.5;
            margin-bottom: 24px;
            animation: fadeIn .3s ease both;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-6px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .alert-error {
            background: var(--red-bg);
            border: 1px solid var(--red-bd);
            color: var(--red);
        }

        .alert-error svg { flex-shrink: 0; margin-top: 1px; }

        /* ── Form group ── */
        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            font-size: .875rem;
            font-weight: 600;
            color: var(--gray-700);
            margin-bottom: 8px;
        }

        .input-wrap {
            position: relative;
        }

        .input-wrap svg {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            width: 18px;
            height: 18px;
            stroke: var(--gray-400);
            pointer-events: none;
            transition: stroke .2s;
        }

        .form-input {
            width: 100%;
            padding: 13px 14px 13px 44px;
            border: 1.5px solid var(--gray-200);
            border-radius: 10px;
            font-size: .95rem;
            font-family: inherit;
            color: var(--gray-900);
            background: var(--gray-50);
            transition: border-color .2s, box-shadow .2s, background .2s;
            outline: none;
            letter-spacing: .5px;
        }

        .form-input::placeholder {
            color: var(--gray-400);
            letter-spacing: 0;
            font-size: .875rem;
        }

        .form-input:focus {
            border-color: var(--orange);
            background: var(--white);
            box-shadow: 0 0 0 4px rgba(232,84,26,.10);
        }

        .form-input:focus + svg,
        .input-wrap:focus-within svg {
            stroke: var(--orange);
        }

        .form-hint {
            margin-top: 7px;
            font-size: .8rem;
            color: var(--gray-500);
        }

        /* ── Submit button ── */
        .btn-verify {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, var(--orange) 0%, var(--orange-lt) 100%);
            color: var(--white);
            border: none;
            border-radius: 10px;
            font-size: .95rem;
            font-weight: 700;
            font-family: inherit;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: transform .15s, box-shadow .15s, filter .15s;
            box-shadow: 0 4px 14px rgba(232,84,26,.35);
            letter-spacing: .2px;
            margin-bottom: 20px;
        }

        .btn-verify:hover {
            filter: brightness(1.06);
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(232,84,26,.40);
        }

        .btn-verify:active {
            transform: translateY(0);
            filter: brightness(.97);
        }

        .btn-verify svg {
            width: 18px; height: 18px;
            stroke: var(--white); fill: none;
            flex-shrink: 0;
        }

        /* ── Back link ── */
        .back-link {
            display: block;
            text-align: center;
            font-size: .875rem;
            color: var(--orange);
            text-decoration: none;
            font-weight: 600;
            transition: color .15s;
        }

        .back-link:hover { color: var(--orange-dk); text-decoration: underline; }

        /* ══════════════════════════════════════════
         |  RESULT CARD — muncul setelah verifikasi
         ══════════════════════════════════════════ */

        .result-card {
            margin-top: 28px;
            border-radius: 14px;
            overflow: hidden;
            border: 1.5px solid var(--green-bd);
            animation: slideUp .4s cubic-bezier(.22,.68,0,1.2) both;
        }

        .result-header {
            background: var(--green-bg);
            padding: 18px 22px;
            display: flex;
            align-items: center;
            gap: 12px;
            border-bottom: 1.5px solid var(--green-bd);
        }

        .result-badge {
            width: 38px; height: 38px;
            background: var(--green);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }

        .result-badge svg {
            width: 20px; height: 20px;
            stroke: var(--white); fill: none; stroke-width: 2.5;
        }

        .result-header-text h3 {
            font-size: .95rem;
            font-weight: 700;
            color: var(--green);
        }

        .result-header-text p {
            font-size: .8rem;
            color: var(--gray-500);
            margin-top: 2px;
        }

        .result-body {
            padding: 22px;
            background: var(--white);
        }

        .result-row {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 16px;
            padding: 10px 0;
            border-bottom: 1px solid var(--gray-100);
            font-size: .875rem;
        }

        .result-row:last-child { border-bottom: none; padding-bottom: 0; }
        .result-row:first-child { padding-top: 0; }

        .result-label {
            color: var(--gray-500);
            font-weight: 500;
            white-space: nowrap;
            flex-shrink: 0;
        }

        .result-value {
            color: var(--gray-900);
            font-weight: 600;
            text-align: right;
        }

        .badge-valid {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            background: var(--green-bg);
            color: var(--green);
            border: 1px solid var(--green-bd);
            padding: 3px 10px;
            border-radius: 20px;
            font-size: .78rem;
            font-weight: 700;
        }

        .badge-valid::before {
            content: '';
            width: 6px; height: 6px;
            background: var(--green);
            border-radius: 50%;
        }

        /* ── Responsive ── */
        @media (max-width: 480px) {
            .card-header  { padding: 32px 24px 28px; }
            .card-body    { padding: 28px 24px 24px; }
            .card-header h1 { font-size: 1.3rem; }
        }
    </style>
</head>
<body>

<div class="card">

    {{-- ── Header oranye ── --}}
    <div class="card-header">
        <div class="header-icon">
            {{-- Ikon badge centang --}}
            <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path d="M12 2l2.09 4.26L18.5 7.27l-3.25 3.17.77 4.47L12 12.77l-4.02 2.14.77-4.47L5.5 7.27l4.41-1.01L12 2z"/>
                <path d="M9 12l2 2 4-4" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" fill="none"/>
            </svg>
        </div>
        <h1>Verifikasi Keaslian Sertifikat</h1>
        <p>Masukkan kode sertifikat untuk memverifikasi keaslian dokumen</p>
    </div>

    {{-- ── Body ── --}}
    <div class="card-body">

        {{-- Alert error --}}
        @if(session('error'))
        <div class="alert alert-error">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
            </svg>
            <span>{{ session('error') }}</span>
        </div>
        @endif

        {{-- Form verifikasi --}}
        <form action="{{ route('sertifikat.cek.kode') }}" method="POST">
            @csrf

            <div class="form-group">
                <label class="form-label" for="kode_sertifikat">Kode Sertifikat</label>
                <div class="input-wrap">
                    <input
                        type="text"
                        id="kode_sertifikat"
                        name="kode_sertifikat"
                        class="form-input"
                        placeholder="Contoh: CERT-K3-001-2026-0001"
                        value="{{ old('kode_sertifikat', request('kode_sertifikat')) }}"
                        autocomplete="off"
                        autofocus
                        style="text-transform: uppercase;"
                    >
                    {{-- Icon search di dalam input --}}
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35" stroke-linecap="round"/>
                    </svg>
                </div>
                @error('kode_sertifikat')
                    <p class="form-hint" style="color: var(--red);">{{ $message }}</p>
                @else
                    <p class="form-hint">Kode sertifikat tercantum di bagian bawah dokumen sertifikat Anda.</p>
                @enderror
            </div>

            <button type="submit" class="btn-verify">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                    <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35" stroke-linecap="round"/>
                </svg>
                Verifikasi Sekarang
            </button>

        </form>

        <a href="{{ route('welcome') }}" class="back-link">← Kembali ke Beranda</a>

        {{-- ══ Hasil Verifikasi ══ --}}
        @isset($sertifikat)
        <div class="result-card">

            <div class="result-header">
                <div class="result-badge">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                        <polyline points="20 6 9 17 4 12"/>
                    </svg>
                </div>
                <div class="result-header-text">
                    <h3>Sertifikat Valid</h3>
                    <p>Dokumen ini telah terverifikasi keasliannya</p>
                </div>
            </div>

            <div class="result-body">

                <div class="result-row">
                    <span class="result-label">Status</span>
                    <span class="result-value">
                        <span class="badge-valid">Terverifikasi</span>
                    </span>
                </div>

                <div class="result-row">
                    <span class="result-label">Kode Sertifikat</span>
                    <span class="result-value" style="font-family: monospace; font-size: .82rem; letter-spacing: .5px;">
                        {{ $sertifikat->kode_sertifikat }}
                    </span>
                </div>

                <div class="result-row">
                    <span class="result-label">Nama Peserta</span>
                    <span class="result-value">
                        {{-- Prioritaskan nama dari akun, fallback ke nama di formulir pendaftaran --}}
                        {{ $sertifikat->pendaftaran->peserta?->nama
                            ?? trim($sertifikat->pendaftaran->first_name . ' ' . $sertifikat->pendaftaran->last_name) }}
                    </span>
                </div>

                <div class="result-row">
                    <span class="result-label">Nama Pelatihan</span>
                    <span class="result-value">{{ $sertifikat->pendaftaran->pelatihan->nama_pelatihan }}</span>
                </div>

                <div class="result-row">
                    <span class="result-label">Kode Pelatihan</span>
                    <span class="result-value">{{ $sertifikat->pendaftaran->pelatihan->kode_pelatihan }}</span>
                </div>

                <div class="result-row">
                    <span class="result-label">Periode</span>
                    <span class="result-value">
                        {{ \Carbon\Carbon::parse($sertifikat->pendaftaran->pelatihan->tgl_mulai)->translatedFormat('d M Y') }}
                        –
                        {{ \Carbon\Carbon::parse($sertifikat->pendaftaran->pelatihan->tgl_selesai)->translatedFormat('d M Y') }}
                    </span>
                </div>

                <div class="result-row">
                    <span class="result-label">Tanggal Terbit</span>
                    <span class="result-value">
                        {{ \Carbon\Carbon::parse($sertifikat->tgl_terbit)->translatedFormat('d F Y') }}
                    </span>
                </div>

                <div class="result-row">
                    <span class="result-label">Diterbitkan Oleh</span>
                    <span class="result-value">{{ $sertifikat->diterbitkan_oleh }}</span>
                </div>

            </div>
        </div>
        @endisset

    </div>{{-- /card-body --}}

</div>{{-- /card --}}

<script>
    // Auto-uppercase input kode sertifikat saat user mengetik
    document.getElementById('kode_sertifikat').addEventListener('input', function () {
        const pos = this.selectionStart;
        this.value = this.value.toUpperCase();
        this.setSelectionRange(pos, pos);
    });
</script>

</body>
</html>