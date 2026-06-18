<?php

use App\Http\Controllers\Web\WelcomeController;
use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\PelatihanController;
use App\Http\Controllers\Web\KategoriController;
use App\Http\Controllers\Web\SesiPelatihanController;
use App\Http\Controllers\Web\PendaftaranController;
use App\Http\Controllers\Web\LogbookController;
use App\Http\Controllers\Web\KualifikasiSertifikasiController;
use App\Http\Controllers\Web\SertifikatController;
use App\Http\Controllers\Web\InstrukturController;
use App\Http\Controllers\Web\KodePenerimaanController;     // ← ditambahkan
use App\Http\Controllers\Web\AkunController;
use App\Http\Controllers\Web\LaporanController;
use Illuminate\Support\Facades\Route;


Route::get('/', [WelcomeController::class, 'index'])->name('welcome');

// Cek sertifikat (publik)
Route::get('/cek-sertifikat',  [SertifikatController::class, 'cekForm'])->name('sertifikat.cek');
Route::post('/cek-sertifikat', [SertifikatController::class, 'cekKode'])->name('sertifikat.cek.kode');

// Autentikasi
Route::middleware('guest')->group(function () {
    Route::get('/login',  [AuthController::class, 'loginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');

    Route::get('/register',  [AuthController::class, 'registerForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post');

    Route::get('/lupa-password',           [AuthController::class, 'forgotForm'])->name('password.forgot');
    Route::post('/lupa-password',          [AuthController::class, 'forgotSend'])->name('password.forgot.send');
    Route::get('/reset-password/{token}',  [AuthController::class, 'resetForm'])->name('password.reset');
    Route::post('/reset-password',         [AuthController::class, 'resetPassword'])->name('password.reset.post');
});

// Logout
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

Route::middleware('auth')->group(function () {

    // Dashboard (role-aware)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profil bersama semua role
    Route::get('/profil',           [AkunController::class, 'profil'])->name('profil');
    Route::post('/profil',          [AkunController::class, 'updateProfil'])->name('profil.update');
    Route::post('/profil/foto',     [AkunController::class, 'updateFoto'])->name('profil.foto');
    Route::post('/profil/password', [AkunController::class, 'gantiPassword'])->name('profil.password');

    /* ══════════════════════════════════════════════════════════════════════
     |  ADMIN
     ══════════════════════════════════════════════════════════════════════ */
    Route::prefix('admin')->name('admin.')->group(function () {

        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Kelola Pelatihan
        Route::resource('pelatihan', PelatihanController::class)
             ->parameters(['pelatihan' => 'pelatihan']);

        // Kelola Sesi Pelatihan
        Route::prefix('pelatihan/{pelatihan}/sesi')->name('sesi.')->group(function () {
            Route::get('/',             [SesiPelatihanController::class, 'index'])->name('index');
            Route::get('/tambah',       [SesiPelatihanController::class, 'create'])->name('create');
            Route::post('/',            [SesiPelatihanController::class, 'store'])->name('store');
            Route::get('/{sesi}/edit',  [SesiPelatihanController::class, 'edit'])->name('edit');
            Route::put('/{sesi}',       [SesiPelatihanController::class, 'update'])->name('update');
            Route::delete('/{sesi}',    [SesiPelatihanController::class, 'destroy'])->name('destroy');
        });

        // Kelola Kategori
        Route::resource('kategori', KategoriController::class)->except(['show']);

        // Kelola Pendaftaran
        Route::get('/pendaftaran',                        [PendaftaranController::class, 'index'])->name('pendaftaran.index');
        Route::get('/pendaftaran/{pendaftaran}',          [PendaftaranController::class, 'show'])->name('pendaftaran.show');
        Route::get('/pendaftaran/{pendaftaran}/detail',   [PendaftaranController::class, 'detail'])->name('pendaftaran.detail');
        Route::post('/pendaftaran/{pendaftaran}/terima',  [PendaftaranController::class, 'terima'])->name('pendaftaran.terima');
        Route::post('/pendaftaran/{pendaftaran}/tolak',   [PendaftaranController::class, 'tolak'])->name('pendaftaran.tolak');
        Route::delete('/pendaftaran/{pendaftaran}',       [PendaftaranController::class, 'destroy'])->name('pendaftaran.destroy');

        // ── Kelola Instruktur ──────────────────────────────────────────────
        Route::resource('instruktur', InstrukturController::class)
             ->parameters(['instruktur' => 'user']);
        Route::post('/instruktur/tugaskan',
             [InstrukturController::class, 'tugaskan'])->name('instruktur.tugaskan');

        Route::patch('/instruktur/{user}/verifikasi',
             [InstrukturController::class, 'verifikasi'])->name('instruktur.verifikasi');

        // ── Kode Penerimaan Instruktur ────────────────────────────────────
        Route::get('/kode-penerimaan',
             [KodePenerimaanController::class, 'index'])->name('kode-penerimaan.index');
        Route::post('/kode-penerimaan',
             [KodePenerimaanController::class, 'store'])->name('kode-penerimaan.store');
        Route::post('/kode-penerimaan/{kodePenerimaan}/kirim-email',
             [KodePenerimaanController::class, 'kirimEmail'])->name('kode-penerimaan.kirim-email');
        Route::delete('/kode-penerimaan/{kodePenerimaan}',
             [KodePenerimaanController::class, 'destroy'])->name('kode-penerimaan.destroy');

        // Sertifikat
        Route::get('/sertifikat',                               [SertifikatController::class, 'index'])->name('sertifikat.index');
        Route::post('/sertifikat/tanda-tangan/{pelatihan}',     [SertifikatController::class, 'uploadTandaTangan'])->name('sertifikat.uploadTandaTangan');
        Route::get('/sertifikat/pelatihan/{pelatihan}/posisi',  [SertifikatController::class, 'getPosisi'])->name('sertifikat.getPosisi');
        Route::post('/sertifikat/pelatihan/{pelatihan}/posisi', [SertifikatController::class, 'savePosisi'])->name('sertifikat.savePosisi');
        Route::get('/sertifikat/preview/{pendaftaran}',         [SertifikatController::class, 'preview'])->name('sertifikat.preview');
        Route::post('/sertifikat/generate/{pendaftaran}',       [SertifikatController::class, 'generate'])->name('sertifikat.generate');
        Route::post('/sertifikat/generate-massal',              [SertifikatController::class, 'generateMassal'])->name('sertifikat.generateMassal');
        Route::post('/sertifikat/template/{pelatihan}',         [SertifikatController::class, 'uploadTemplate'])->name('sertifikat.uploadTemplate');
        Route::delete('/sertifikat/{sertifikat}',               [SertifikatController::class, 'destroy'])->name('sertifikat.destroy');

        // Kualifikasi & Logbook (read-only)
        Route::get('/kualifikasi', [KualifikasiSertifikasiController::class, 'adminIndex'])->name('kualifikasi.index');
        Route::get('/logbook',     [LogbookController::class, 'adminIndex'])->name('logbook.index');

        // Laporan
        Route::get('/laporan',              [LaporanController::class, 'index'])->name('laporan.index');
        Route::get('/laporan/export',       [LaporanController::class, 'export'])->name('laporan.export');
        Route::get('/laporan/exportPdf',    [LaporanController::class, 'exportPdf'])->name('laporan.exportPdf');
        Route::get('/laporan/export-excel', [LaporanController::class, 'exportExcel'])->name('laporan.exportExcel');

        // Kelola Akun
        Route::resource('akun', AkunController::class)->parameters(['akun' => 'user']);
    });

    /* ══════════════════════════════════════════════════════════════════════
     |  INSTRUKTUR
     ══════════════════════════════════════════════════════════════════════ */
    Route::prefix('instruktur')->name('instruktur.')->group(function () {

        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        Route::get('/pelatihan',             [PelatihanController::class, 'pelatihanSaya'])->name('pelatihan.index');
        Route::get('/pelatihan/{pelatihan}', [PelatihanController::class, 'detailPelatihanSaya'])->name('pelatihan.detail');

        Route::get('/sesi',        [SesiPelatihanController::class, 'jadwalSesi'])->name('sesi.index');
        Route::get('/sesi/{sesi}', [SesiPelatihanController::class, 'detailSesi'])->name('sesi.detail');

        Route::get('/logbook',                   [LogbookController::class, 'index'])->name('logbook.index');
        Route::get('/logbook/{sesi}/input',      [LogbookController::class, 'inputKehadiran'])->name('logbook.input');
        Route::post('/logbook/{sesi}/simpan',    [LogbookController::class, 'simpanKehadiran'])->name('logbook.simpan');
        Route::get('/logbook/rekap/{pelatihan}', [LogbookController::class, 'rekapKehadiran'])->name('logbook.rekap');

        Route::get('/kelayakan',                       [KualifikasiSertifikasiController::class, 'index'])->name('kelayakan.index');
        Route::post('/kelayakan/{pendaftaran}',        [KualifikasiSertifikasiController::class, 'simpan'])->name('kelayakan.simpan');
        Route::post('/kelayakan/massal/{pelatihan}',   [KualifikasiSertifikasiController::class, 'simpanMassal'])->name('kelayakan.massal');
        Route::get('/kelayakan/riwayat',               [KualifikasiSertifikasiController::class, 'riwayatSertifInstruktur'])->name('kelayakan.riwayat');

        Route::get('/sertifikat', [SertifikatController::class, 'riwayatInstruktur'])->name('sertifikat.index');
    });

    /* ══════════════════════════════════════════════════════════════════════
     |  PESERTA
     ══════════════════════════════════════════════════════════════════════ */
    Route::prefix('peserta')->name('peserta.')->group(function () {

        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        Route::get('/pelatihan',             [PelatihanController::class, 'katalog'])->name('pelatihan.index');
        Route::get('/pelatihan/{pelatihan}', [PelatihanController::class, 'detailKatalog'])->name('pelatihan.detail');

        Route::get('/pendaftaran/{pelatihan}',  [PendaftaranController::class, 'formPendaftaran'])->name('pendaftaran.index');
        Route::post('/pendaftaran/{pelatihan}', [PendaftaranController::class, 'kirimPendaftaran'])->name('pendaftaran.kirim');

        Route::get('/riwayat',                         [PendaftaranController::class, 'riwayat'])->name('riwayat.index');
        Route::get('/riwayat/{pendaftaran}/detail',    [PendaftaranController::class, 'detailPendaftaran'])->name('riwayat.detail');
        Route::get('/riwayat/{pendaftaran}/pdf',       [PendaftaranController::class, 'exportPDF'])->name('riwayat.exportPDF');

        Route::get('/kehadiran', [SesiPelatihanController::class, 'statusKehadiran'])->name('kehadiran.index');
        Route::get('/kelayakan', [KualifikasiSertifikasiController::class, 'statusKelayakanPeserta'])->name('kelayakan');

        Route::get('/sertifikat',                       [SertifikatController::class, 'sertifSaya'])->name('sertifikat.index');
        Route::get('/sertifikat/{sertifikat}/download', [SertifikatController::class, 'download'])->name('sertifikat.download');
    });
});