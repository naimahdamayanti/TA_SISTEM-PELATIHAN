<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\PelatihanController;
use App\Http\Controllers\Api\SesiPelatihanController;
use App\Http\Controllers\Api\PendaftaranController;
use App\Http\Controllers\Api\SertifikatController;
use App\Http\Controllers\Api\LogbookController;

Route::get('/test', function () {
    return response()->json(['status' => 'ok']);
});
// AUTH
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// PROTECTED
// Route::middleware('auth:sanctum')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout']);
    Route::apiResource('users', UserController::class)->names('api.users');
    Route::apiResource('pelatihan', PelatihanController::class)->names('api.pelatihan');
    Route::apiResource('sesi', SesiPelatihanController::class)->names('api.sesi');
    Route::apiResource('logbook', LogbookController::class)->names('api.logbook');

    Route::get('pendaftaran', [PendaftaranController::class, 'index']);
    Route::post('pendaftaran', [PendaftaranController::class, 'store']);
    Route::patch('pendaftaran/{id}/approve', [PendaftaranController::class, 'approve']);
    Route::delete('pendaftaran/{id}', [PendaftaranController::class, 'destroy']);

    Route::get('sertifikat', [SertifikatController::class, 'index']);
    Route::post('sertifikat', [SertifikatController::class, 'store']);
    Route::get('sertifikat/cek/{kode}', [SertifikatController::class, 'cek']);
// });