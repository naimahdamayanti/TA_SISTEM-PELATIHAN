<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar - Silahkan Daftar Disini</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(to bottom, #ff0000ff 0%, #080301ff 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .container {
            display: flex;
            max-width: 1000px;
            width: 100%;
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        }

        /* ── Left panel ─────────────────────────── */
        .left-section {
            background: #ff0000ff;
            color: white;
            padding: 60px 40px;
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
            overflow: hidden;
        }

        .left-section::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            animation: pulse 15s ease-in-out infinite;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50%       { transform: scale(1.1); }
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 14px;
            margin-bottom: 20px;
            position: relative;
            z-index: 1;
        }

        .logo img { height: 40px; width: auto; }
        .logo i   { font-size: 20px; }

        .left-content { position: relative; z-index: 1; }

        h1 {
            font-size: 42px;
            font-weight: 700;
            margin-bottom: 30px;
            line-height: 1.2;
        }

        .illustration { width: 100%; max-width: 350px; margin: 0 auto; }
        .illustration img { width: 100%; height: auto; }

        /* ── Right panel ────────────────────────── */
        .right-section {
            flex: 1;
            padding: 50px 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            overflow-y: auto;
        }

        h2 {
            font-size: 28px;
            color: #333;
            margin-bottom: 6px;
        }

        .subtitle {
            color: #666;
            margin-bottom: 24px;
            font-size: 14px;
        }

        /* ── Form elements ──────────────────────── */
        .form-group { margin-bottom: 16px; }

        label {
            display: block;
            color: #333;
            font-size: 14px;
            font-weight: 500;
            margin-bottom: 6px;
        }

        input[type="text"],
        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 11px 14px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.3s;
            background: #f8f9fa;
        }

        input[type="text"]:focus,
        input[type="email"]:focus,
        input[type="password"]:focus {
            outline: none;
            border-color: #e73c3c;
            background: white;
            box-shadow: 0 0 0 3px rgba(231, 60, 60, 0.1);
        }

        /* ── File input ─────────────────────────── */
        .file-input-wrapper {
            position: relative;
        }

        input[type="file"] {
            width: 100%;
            padding: 9px 14px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 13px;
            background: #f8f9fa;
            color: #555;
            cursor: pointer;
            transition: all 0.3s;
        }

        input[type="file"]:focus {
            outline: none;
            border-color: #e73c3c;
            background: white;
            box-shadow: 0 0 0 3px rgba(231, 60, 60, 0.1);
        }

        /* ── Radio group ────────────────────────── */
        .radio-group {
            display: flex;
            gap: 24px;
            margin-top: 4px;
        }

        .radio-option {
            display: flex;
            align-items: center;
            gap: 7px;
            cursor: pointer;
            padding: 8px 18px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 14px;
            color: #444;
            transition: all 0.25s;
            user-select: none;
        }

        .radio-option:hover {
            border-color: #e73c3c;
            background: #fff5f5;
        }

        .radio-option input[type="radio"] {
            accent-color: #e73c3c;
            width: 15px;
            height: 15px;
            cursor: pointer;
        }

        .radio-option.selected {
            border-color: #e73c3c;
            background: #fff5f5;
            color: #c92a2a;
            font-weight: 600;
        }

        /* ── Info box instruktur ────────────────── */
        .instruktur-info {
            background: #fff8e1;
            border-left: 4px solid #f59e0b;
            border-radius: 0 8px 8px 0;
            padding: 12px 14px;
            margin-bottom: 16px;
            font-size: 13px;
            color: #78350f;
            line-height: 1.6;
            display: none; /* ditampilkan via JS */
        }

        .instruktur-info i { margin-right: 6px; }

        /* ── Instruktur extra fields ────────────── */
        #instruktur-fields {
            display: none; /* ditampilkan via JS */
            border-top: 1px dashed #e0e0e0;
            padding-top: 14px;
            margin-top: 4px;
        }

        /* ── Error / help text ──────────────────── */
        .text-danger {
            color: #c33;
            font-size: 12px;
            margin-top: 4px;
            display: block;
        }

        .form-text {
            color: #888;
            font-size: 12px;
            margin-top: 4px;
        }

        /* ── Alert ──────────────────────────────── */
        .alert {
            padding: 10px 14px;
            border-radius: 8px;
            margin-bottom: 16px;
            font-size: 14px;
        }

        .alert-success { background: #efe; color: #3c3; }
        .alert-danger  { background: #fee; color: #c33; }
        .alert-info    { background: #e8f4fd; color: #1a6fa8; }

        /* ── Submit button ──────────────────────── */
        .btn-submit {
            width: 100%;
            padding: 13px;
            background: linear-gradient(135deg, #e73c3c 0%, #c92a2a 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
            margin-top: 8px;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(231, 60, 60, 0.3);
        }

        /* ── Login link ─────────────────────────── */
        .login-link {
            text-align: center;
            margin-top: 18px;
            font-size: 14px;
            color: #666;
        }

        .login-link a {
            color: #e73c3c;
            text-decoration: none;
            font-weight: 600;
        }

        .login-link a:hover { text-decoration: underline; }

        /* ── Responsive ─────────────────────────── */
        @media (max-width: 768px) {
            .container { flex-direction: column; }
            .left-section { padding: 40px 30px; }
            h1 { font-size: 32px; }
            .right-section { padding: 36px 28px; }
        }
    </style>
</head>

<body>
<div class="container">

    {{-- ── Left panel ──────────────────────────────── --}}
    <div class="left-section">
        <div class="logo">
            <img src="{{ asset('template/assets/img/logo/logo-expertindo.png') }}" alt="logo" />
        </div>
        <div class="left-content">
            <h1>Silahkan Daftar<br>Disini!</h1>
            <img src="{{ asset('template/assets/img/logo/login.png') }}" alt="ilustrasi daftar">
        </div>
    </div>

    {{-- ── Right panel ─────────────────────────────── --}}
    <div class="right-section">
        <h2>Daftar</h2>
        <p class="subtitle">Silahkan lengkapi data di bawah ini!</p>

        {{-- Flash messages --}}
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        @if(session('info'))
            <div class="alert alert-info">{{ session('info') }}</div>
        @endif

        {{-- ── Form ──────────────────────────────────── --}}
        <form action="{{ route('register.post') }}" method="POST" enctype="multipart/form-data">
            @csrf

            {{-- Nama --}}
            <div class="form-group">
                <label for="nama">Nama Lengkap</label>
                <input type="text" id="nama" name="nama"
                       placeholder="Masukkan nama lengkap"
                       value="{{ old('nama') }}" required>
                @error('nama')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            {{-- Username --}}
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username"
                       placeholder="Masukkan username"
                       value="{{ old('username') }}" required>
                @error('username')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            {{-- Email --}}
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email"
                       placeholder="Masukkan email"
                       value="{{ old('email') }}" required>
                @error('email')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            {{-- Password --}}
            <div class="form-group">
                <label for="password">Kata Sandi</label>
                <input type="password" id="password" name="password"
                       placeholder="Masukkan kata sandi" required>
                @error('password')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            {{-- No HP --}}
            <div class="form-group">
                <label for="no_hp">No. HP</label>
                <input type="text" id="no_hp" name="no_hp"
                       placeholder="Masukkan nomor HP"
                       value="{{ old('no_hp') }}">
                @error('no_hp')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            {{-- Role --}}
            <div class="form-group">
                <label>Daftar sebagai</label>
                <div class="radio-group">
                    <label class="radio-option {{ old('role') === 'peserta' ? 'selected' : '' }}">
                        <input type="radio" name="role" value="peserta"
                               {{ old('role', 'peserta') === 'peserta' ? 'checked' : '' }} required>
                        <i class="fas fa-user"></i> Peserta
                    </label>
                    <label class="radio-option {{ old('role') === 'instruktur' ? 'selected' : '' }}">
                        <input type="radio" name="role" value="instruktur"
                               {{ old('role') === 'instruktur' ? 'checked' : '' }}>
                        <i class="fas fa-chalkboard-teacher"></i> Instruktur
                    </label>
                </div>
                @error('role')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            {{-- ── Field khusus instruktur ──────────────── --}}
            <div class="instruktur-info" id="instruktur-info">
                <i class="fas fa-info-circle"></i>
                Registrasi instruktur hanya tersedia bagi yang telah diterima melalui proses seleksi
                <strong>PT. Expertindo Training</strong>. Pastikan Anda memiliki
                <strong>kode penerimaan</strong> dan <strong>surat/bukti penerimaan</strong>
                sebelum melanjutkan.
            </div>

            <div id="instruktur-fields">

                {{-- Kode penerimaan --}}
                <div class="form-group">
                    <label for="kode_penerimaan">
                        Kode Penerimaan <span style="color:#e73c3c;">*</span>
                    </label>
                    <input type="text" id="kode_penerimaan" name="kode_penerimaan"
                           placeholder="Contoh: EXP-INSTR-XXXXXX"
                           value="{{ old('kode_penerimaan') }}"
                           style="text-transform:uppercase;letter-spacing:2px;font-family:monospace;font-size:15px;">
                    <span class="form-text">Kode dikirimkan oleh Admin melalui email setelah proses seleksi.</span>
                    @error('kode_penerimaan')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Bukti penerimaan --}}
                <div class="form-group">
                    <label for="bukti_penerimaan">
                        Bukti Penerimaan <span style="color:#e73c3c;">*</span>
                    </label>
                    <div class="file-input-wrapper">
                        <input type="file" id="bukti_penerimaan" name="bukti_penerimaan"
                               accept=".pdf,.jpg,.jpeg,.png">
                    </div>
                    <span class="form-text">
                        <i class="fas fa-paperclip"></i>
                        Upload surat penerimaan, SK, atau kontrak dari PT. Expertindo Training.
                        (PDF / JPG / PNG, maks. 2 MB)
                    </span>
                    @error('bukti_penerimaan')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

            </div>
            {{-- ── End field instruktur ────────────────── --}}

            <button type="submit" class="btn-submit">
                <i class="fas fa-user-plus"></i> Daftar
            </button>
        </form>

        <div class="login-link">
            Sudah Punya Akun? <a href="{{ route('login') }}">Login disini</a>
        </div>
    </div>

</div>

<script>
    const radios        = document.querySelectorAll('input[name="role"]');
    const instrFields   = document.getElementById('instruktur-fields');
    const instrInfo     = document.getElementById('instruktur-info');
    const radioLabels   = document.querySelectorAll('.radio-option');
    const kodeInput     = document.getElementById('kode_penerimaan');
    const buktiInput    = document.getElementById('bukti_penerimaan');

    function toggleInstrukturFields() {
        const isInstruktur = document.querySelector('input[name="role"]:checked')?.value === 'instruktur';

        // Show/hide extra fields
        instrFields.style.display = isInstruktur ? 'block' : 'none';
        instrInfo.style.display   = isInstruktur ? 'block' : 'none';

        // Required hanya saat instruktur dipilih
        kodeInput.required  = isInstruktur;
        buktiInput.required = isInstruktur;

        // Clear nilai jika beralih ke peserta
        if (!isInstruktur) {
            kodeInput.value  = '';
            buktiInput.value = '';
        }

        // Update tampilan label radio (selected state)
        radioLabels.forEach(label => {
            const radio = label.querySelector('input[type="radio"]');
            label.classList.toggle('selected', radio.checked);
        });
    }

    // Auto-uppercase kode penerimaan
    kodeInput.addEventListener('input', function () {
        const pos = this.selectionStart;
        this.value = this.value.toUpperCase();
        this.setSelectionRange(pos, pos);
    });

    radios.forEach(r => r.addEventListener('change', toggleInstrukturFields));

    toggleInstrukturFields();
</script>

</body>
</html>