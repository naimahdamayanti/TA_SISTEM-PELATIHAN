<!DOCTYPE html>
<html class="no-js" lang="">
  <head>
    <meta charset="utf-8" />
    <meta http-equiv="x-ua-compatible" content="ie=edge" />
    <title>Expertindo Training</title>
    <meta name="description" content="" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="shortcut icon" href="{{ asset('template/assets/img/logo/logo-expertindo.png') }}">

    <!-- Place favicon.ico in the root directory -->

    <!-- ========================= CSS here ========================= -->
    <link rel="stylesheet" href="{{ asset('template/assets/css/bootstrap-5.0.0-beta2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('template/assets/css/LineIcons.2.0.css') }}">
    <link rel="stylesheet" href="{{ asset('template/assets/css/tiny-slider.css') }}">
    <link rel="stylesheet" href="{{ asset('template/assets/css/animate.css') }}">
    <link rel="stylesheet" href="{{ asset('template/assets/css/main.css') }}">

  </head>
  <body>

    <!-- <div class="preloader">
      <div class="loader">
        <div class="spinner">
          <div class="spinner-container">
            <div class="spinner-rotator">
              <div class="spinner-left">
                <div class="spinner-circle"></div>
              </div>
              <div class="spinner-right">
                <div class="spinner-circle"></div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
		preloader end -->
		

    <!-- ========================= header start ========================= -->
    <header class="header">
      <div class="navbar-area">
        <div class="container">
          <div class="row align-items-center">
            <div class="col-lg-12">
              <nav class="navbar navbar-expand-lg">
                <a class="navbar-brand" href="index.html">
                  <img src="{{ asset('template/assets/img/logo/logo-expertindo.png') }}" alt="logo" />
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                  <span class="toggler-icon"></span>
                  <span class="toggler-icon"></span>
                  <span class="toggler-icon"></span>
                </button>
                <!-- navbar collapse -->
              </nav>
              <!-- navbar -->
            </div>
          </div>
          <!-- row -->
        </div>
        <!-- container -->
      </div>
      <!-- navbar area -->
    </header>
    <!-- ========================= header end ========================= -->

    <!-- ========================= hero-section start ========================= -->
    <section id="home" class="hero-section">
      <div class="container">
        <div class="row align-items-center">
          <div class="col-lg-6">
            <div class="hero-content">
							<span class="wow fadeInLeft" data-wow-delay=".2s">Selamat Datang di Expertindo</span>
              <h1 class="wow fadeInUp" data-wow-delay=".4s">
								Pelatihan dan Sertifikasi untuk Kemajuan Anda.
							</h1>
              <p class="wow fadeInUp" data-wow-delay=".6s">
                Belajar bersama instruktur berpengalaman dengan materi terkini dan relevan dengan industri.
              </p>

              <div class="col-lg-6">
						<div class="hero-img wow fadeInUp" data-wow-delay=".5s">
							<img src="{{ asset('template/assets/img/logo/logo-expertindo.png') }}" alt="logo">
						</div>
					</div>
                    
							<a href="{{ route('register') }}" class="main-btn btn-hover wow fadeInUp" data-wow-delay=".6s">Daftar</a>
              <a href="{{ route('login') }}" class="main-btn btn-hover wow fadeInUp" data-wow-delay=".6s">Login</a>
            </div>
					</div>
					
        </div>
			</div>
    </section>
  </body>
</html>
