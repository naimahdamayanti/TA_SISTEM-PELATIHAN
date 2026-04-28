<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\WelcomeController;
use App\Http\Controllers\Web\LoginController;
use App\Http\Controllers\Web\RegisterController;
use App\Http\Controllers\Web\DashboardController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::get('/', [WelcomeController::class, 'index'])->name('welcome');
Route::get('/auth/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/auth/login', [LoginController::class, 'login'])->name('login.submit');
Route::post('/auth/logout', [LoginController::class, 'logout'])->name('logout');
Route::get('/auth/register', [RegisterController::class, 'showRegisterForm'])->name('register');
Route::post('/auth/register', [RegisterController::class, 'register'])->name('register.submit');
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
