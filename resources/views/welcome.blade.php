<!DOCTYPE html>
<html class="no-js" lang="id">
  <head>
    <meta charset="utf-8" />
    <meta http-equiv="x-ua-compatible" content="ie=edge" />
    <title>Expertindo Training</title>
    <meta name="description" content="Pelatihan dan Sertifikasi untuk Kemajuan Anda" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="shortcut icon" href="{{ asset('template/assets/img/logo/logo-expertindo.png') }}">

    <link rel="stylesheet" href="{{ asset('template/assets/css/bootstrap-5.0.0-beta2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('template/assets/css/LineIcons.2.0.css') }}">
    <link rel="stylesheet" href="{{ asset('template/assets/css/tiny-slider.css') }}">
    <link rel="stylesheet" href="{{ asset('template/assets/css/animate.css') }}">
    <link rel="stylesheet" href="{{ asset('template/assets/css/main.css') }}">

    <style>
      .btn-cek-sertifikat {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background-color: transparent;
        border: 2px solid var(--white);
        color: var(--white);
        padding: 12px 30px;
        border-radius: 5px;
        font-size: 16px;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.3s ease;
        margin-left: 12px;
      }

      .btn-cek-sertifikat:hover {
        background-color: var(--white);
        color: var(--primary-color);
      }

      .btn-cek-sertifikat i {
        font-size: 18px;
      }

      /* Modal cek sertifikat */
      .modal-sertifikat .modal-header {
        background-color: var(--primary-color);
        color: var(--white);
        border-bottom: none;
        border-radius: 10px 10px 0 0;
      }

      .modal-sertifikat .modal-header .btn-close {
        filter: invert(1);
      }

      .modal-sertifikat .modal-content {
        border-radius: 10px;
        border: none;
        box-shadow: 0 10px 40px rgba(0,0,0,0.15);
      }

      .modal-sertifikat .form-control {
        border-radius: 5px;
        border: 1px solid #ddd;
        padding: 12px 16px;
        font-size: 15px;
      }

      .modal-sertifikat .form-control:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(var(--primary-color-rgb), 0.15);
      }

      .modal-sertifikat .btn-submit {
        background-color: var(--primary-color);
        color: var(--white);
        border: none;
        padding: 12px 30px;
        border-radius: 5px;
        font-weight: 600;
        font-size: 15px;
        width: 100%;
        transition: 0.3s;
      }

      .modal-sertifikat .btn-submit:hover {
        opacity: 0.88;
      }

      /* Hasil sertifikat */
      .sertifikat-result {
        display: none;
        margin-top: 20px;
        padding: 20px;
        border-radius: 8px;
        border: 1px solid #e0e0e0;
        background: #f9f9f9;
      }

      .sertifikat-result.valid {
        border-color: #28a745;
        background: #f0fff4;
      }

      .sertifikat-result.invalid {
        border-color: #dc3545;
        background: #fff5f5;
      }

      .sertifikat-result .result-icon {
        font-size: 40px;
        margin-bottom: 10px;
      }

      .sertifikat-result .result-icon.valid {
        color: #28a745;
      }

      .sertifikat-result .result-icon.invalid {
        color: #dc3545;
      }

      .sertifikat-detail-item {
        display: flex;
        justify-content: space-between;
        padding: 6px 0;
        border-bottom: 1px solid #e8e8e8;
        font-size: 14px;
      }

      .sertifikat-detail-item:last-child {
        border-bottom: none;
      }

      .sertifikat-detail-item .label {
        color: #666;
        font-weight: 500;
      }

      .sertifikat-detail-item .value {
        color: #222;
        font-weight: 600;
        text-align: right;
      }
    </style>
  </head>

  <body>

    <header class="header">
      <div class="navbar-area">
        <div class="container">
          <div class="row align-items-center">
            <div class="col-lg-12">
              <nav class="navbar navbar-expand-lg">
                <a class="navbar-brand" href="{{ url('/') }}">
                  <img src="{{ asset('template/assets/img/logo/logo-expertindo.png') }}" alt="logo" />
                </a>
                
              </nav>
            </div>
          </div>
        </div>
      </div>
    </header>

    <section id="home" class="hero-section">
      <div class="container">
        <div class="row align-items-center">

          <div class="col-lg-6 order-1 order-lg-1">
            <div class="hero-content">
              <span>Selamat Datang di Expertindo</span>

              <h1>
                Pelatihan dan Sertifikasi untuk Kemajuan Anda.
              </h1>

              <p>
                Belajar bersama instruktur berpengalaman dengan materi terkini dan relevan dengan industri.
              </p>
            </div>
          </div>

          <div class="col-lg-6 text-center order-2 order-lg-2">
            <div class="hero-img">
              <img src="{{ asset('template/assets/img/logo/logo-expertindo.png') }}"
                class="img-fluid" alt="logo">
            </div>
          </div>

          <div class="col-12 order-3 mt-3">
            <a href="{{ route('login') }}" class="main-btn">
              Login
            </a>

            <a href="{{ route('sertifikat.cek') }}" class="main-btn">
              Cek Sertifikat
            </a>
          </div>

        </div>
      </div>
    </section>
  </body>
</html>