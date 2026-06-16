<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Kata Sandi - ExpertIndo Training</title>
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
            background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
            animation: pulse 15s ease-in-out infinite;
        }

        @keyframes pulse {
            0%, 100% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.1);
            }
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

        .logo img {
            height: 40px;
            width: auto;
        }

        .left-content {
            position: relative;
            z-index: 1;
        }

        h1 {
            font-size: 42px;
            font-weight: 700;
            margin-bottom: 30px;
            line-height: 1.2;
        }

        .illustration {
            width: 100%;
            max-width: 350px;
            margin: 0 auto;
        }

        .illustration img {
            width: 100%;
            height: auto;
        }

        .right-section {
            flex: 1;
            padding: 60px 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        h2 {
            font-size: 28px;
            color: #333;
            margin-bottom: 10px;
        }

        .subtitle {
            color: #666;
            margin-bottom: 30px;
            font-size: 14px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            color: #333;
            font-size: 14px;
            font-weight: 500;
            margin-bottom: 8px;
        }

        input[type="password"] {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.3s;
            background: #f8f9fa;
        }

        input[type="password"]:focus {
            outline: none;
            border-color: #e73c3c;
            background: white;
            box-shadow: 0 0 0 3px rgba(231, 60, 60, 0.1);
        }

        .btn-primary {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #e73c3c 0%, #c92a2a 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
            margin-top: 10px;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(231, 60, 60, 0.3);
        }

        .login-link {
            text-align: center;
            margin-top: 20px;
            font-size: 14px;
            color: #666;
        }

        .login-link a {
            color: #e73c3c;
            text-decoration: none;
            font-weight: 600;
        }

        .login-link a:hover {
            text-decoration: underline;
        }

        .error-message {
            background: #fee;
            color: #c33;
            padding: 12px;
            border-radius: 8px;
            border-left: 4px solid #c92a2a;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .error-message ul {
            list-style-position: inside;
            margin: 0;
            padding: 0;
        }

        @media (max-width: 768px) {
            .container {
                flex-direction: column;
            }

            .left-section {
                padding: 40px 30px;
            }

            h1 {
                font-size: 32px;
            }

            .right-section {
                padding: 40px 30px;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="left-section">
            <div class="logo">
                <img src="{{ asset('template/assets/img/logo/logo-expertindo.png') }}" alt="logo" />
            </div>

            <div class="left-content">
                <h1>Atur Ulang Kata Sandi</h1>
                <div class="illustration">
                    <img src="{{ asset('template/assets/img/logo/login.png') }}" alt="logo">
                </div>
            </div>
        </div>

        <div class="right-section">
            <h2>Buat Kata Sandi Baru</h2>
            <p class="subtitle">Amankan kembali akun Anda dengan memasukkan kata sandi yang baru.</p>

            @if ($errors->any())
                <div class="error-message">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ url('/reset-password') }}">
                @csrf
                
                <input type="hidden" name="token" value="{{ $token }}">

                <div class="form-group">
                    <label for="password">Kata Sandi Baru</label>
                    <input type="password" name="password" id="password" placeholder="Minimal 6 karakter" required minlength="6">
                </div>

                <div class="form-group">
                    <label for="password_confirmation">Konfirmasi Kata Sandi Baru</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" placeholder="Ulangi kata sandi" required minlength="6">
                </div>

                <button type="submit" class="btn-primary">Perbarui Kata Sandi</button>
            </form>

            <div class="login-link">
                <a href="{{ route('login') }}">Kembali ke Login</a>
            </div>
        </div>
    </div>
</body>

</html>