<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\PelatihanController;
use App\Http\Controllers\Api\KategoriController;
use App\Http\Controllers\Api\SesiPelatihanController;
use App\Http\Controllers\Api\PendaftaranController;
use App\Http\Controllers\Api\LogbookController;
use App\Http\Controllers\Api\KualifikasiSertifikasiController;
use App\Http\Controllers\Api\SertifikatController;
use App\Http\Controllers\Api\AkunController;
use App\Http\Controllers\Api\LaporanController;
use Illuminate\Support\Facades\Route;


// Autentikasi
Route::post('/login',          [AuthController::class, 'login'])->name('api.login');
Route::post('/register',       [AuthController::class, 'register'])->name('api.register');
Route::post('/forgot-password',[AuthController::class, 'forgotPassword'])->name('api.password.forgot');
Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('api.password.reset');

// Verifikasi sertifikat (publik, tanpa login)
Route::get('/sertifikat/cek',  [SertifikatController::class, 'cek'])->name('api.sertifikat.cek');

// API yang membutuhkan autentikasi
Route::middleware('auth:sanctum')->group(function () {

    /* ── Auth ── */
    Route::post('/logout', [AuthController::class, 'logout'])->name('api.logout');
    Route::get('/me',      [AuthController::class, 'me'])->name('api.me');

    /* ── Dashboard (role-aware) ── */
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('api.dashboard');

    /* ── Profil (semua role) ── */
    Route::get('/profil',           [AkunController::class, 'profil'])->name('api.profil');
    Route::put('/profil',           [AkunController::class, 'updateProfil'])->name('api.profil.update');
    Route::post('/profil/foto',     [AkunController::class, 'updateFoto'])->name('api.profil.foto');
    Route::post('/profil/password', [AkunController::class, 'gantiPassword'])->name('api.profil.password');

    Route::get('/pelatihan/kategori',   [PelatihanController::class, 'kategori'])->name('api.pelatihan.kategori');
    Route::get('/pelatihan',            [PelatihanController::class, 'index'])->name('api.pelatihan.index');
    Route::get('/pelatihan/{id}',       [PelatihanController::class, 'show'])->name('api.pelatihan.show');
    Route::post('/pelatihan',           [PelatihanController::class, 'store'])->name('api.pelatihan.store');       // admin
    Route::put('/pelatihan/{id}',       [PelatihanController::class, 'update'])->name('api.pelatihan.update');    // admin
    Route::delete('/pelatihan/{id}',    [PelatihanController::class, 'destroy'])->name('api.pelatihan.destroy');  // admin

    Route::get('/kategori',           [KategoriController::class, 'index'])->name('api.kategori.index');
    Route::get('/kategori/aktif',     [KategoriController::class, 'aktif'])->name('api.kategori.aktif');
    Route::get('/kategori/{id}',      [KategoriController::class, 'show'])->name('api.kategori.show');
    Route::post('/kategori',          [KategoriController::class, 'store'])->name('api.kategori.store');      // admin
    Route::put('/kategori/{id}',      [KategoriController::class, 'update'])->name('api.kategori.update');   // admin
    Route::delete('/kategori/{id}',   [KategoriController::class, 'destroy'])->name('api.kategori.destroy'); // admin

    Route::get('/sesi',             [SesiPelatihanController::class, 'index'])->name('api.sesi.index');
    Route::get('/sesi/{id}',        [SesiPelatihanController::class, 'show'])->name('api.sesi.show');
    Route::post('/sesi',            [SesiPelatihanController::class, 'store'])->name('api.sesi.store');       // admin
    Route::put('/sesi/{id}',        [SesiPelatihanController::class, 'update'])->name('api.sesi.update');    // admin
    Route::delete('/sesi/{id}',     [SesiPelatihanController::class, 'destroy'])->name('api.sesi.destroy'); // admin

    Route::get('/pendaftaran',                      [PendaftaranController::class, 'index'])->name('api.pendaftaran.index');
    Route::get('/pendaftaran/{id}',                 [PendaftaranController::class, 'show'])->name('api.pendaftaran.show');
    Route::post('/pendaftaran',                     [PendaftaranController::class, 'store'])->name('api.pendaftaran.store');          // peserta
    Route::post('/pendaftaran/{id}/terima',         [PendaftaranController::class, 'terima'])->name('api.pendaftaran.terima');        // admin
    Route::post('/pendaftaran/{id}/tolak',          [PendaftaranController::class, 'tolak'])->name('api.pendaftaran.tolak');          // admin
    Route::delete('/pendaftaran/{id}',              [PendaftaranController::class, 'destroy'])->name('api.pendaftaran.destroy');      // admin

    Route::get('/logbook',                          [LogbookController::class, 'index'])->name('api.logbook.index');
    Route::get('/logbook/rekap/{pelatihan_id}',     [LogbookController::class, 'rekap'])->name('api.logbook.rekap');
    Route::get('/logbook/sesi/{sesi_id}',           [LogbookController::class, 'detailSesi'])->name('api.logbook.detail');
    Route::post('/logbook/sesi/{sesi_id}',          [LogbookController::class, 'simpanKehadiran'])->name('api.logbook.simpan');    // instruktur

    Route::get('/kualifikasi',                          [KualifikasiSertifikasiController::class, 'index'])->name('api.kualifikasi.index');
    Route::get('/kualifikasi/pelatihan/{id}',           [KualifikasiSertifikasiController::class, 'pesertaPerPelatihan'])->name('api.kualifikasi.pelatihan');
    Route::post('/kualifikasi/{pendaftaran_id}',        [KualifikasiSertifikasiController::class, 'simpan'])->name('api.kualifikasi.simpan');          // instruktur
    Route::post('/kualifikasi/massal/{pelatihan_id}',   [KualifikasiSertifikasiController::class, 'simpanMassal'])->name('api.kualifikasi.massal');    // instruktur

    Route::get('/sertifikat',                               [SertifikatController::class, 'index'])->name('api.sertifikat.index');
    Route::get('/sertifikat/{id}/download',                 [SertifikatController::class, 'download'])->name('api.sertifikat.download');
    Route::post('/sertifikat/generate/{pendaftaran_id}',    [SertifikatController::class, 'generate'])->name('api.sertifikat.generate');           // admin
    Route::post('/sertifikat/generate-massal/{pel_id}',     [SertifikatController::class, 'generateMassal'])->name('api.sertifikat.generateMassal'); // admin
    Route::delete('/sertifikat/{id}',                       [SertifikatController::class, 'destroy'])->name('api.sertifikat.destroy');              // admin

    Route::get('/akun',         [AkunController::class, 'index'])->name('api.akun.index');
    Route::get('/akun/{id}',    [AkunController::class, 'show'])->name('api.akun.show');
    Route::post('/akun',        [AkunController::class, 'store'])->name('api.akun.store');
    Route::put('/akun/{id}',    [AkunController::class, 'update'])->name('api.akun.update');
    Route::delete('/akun/{id}', [AkunController::class, 'destroy'])->name('api.akun.destroy');

    Route::get('/laporan',        [LaporanController::class, 'index'])->name('api.laporan.index');
    Route::get('/laporan/export', [LaporanController::class, 'export'])->name('api.laporan.export');
});
