<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WaliController;
use App\Http\Controllers\SiswaController;
use App\Http\Controllers\WaliSiswaController;
use App\Http\Controllers\BerandaWaliController;
use App\Http\Controllers\BerandaOperatorController;
use App\Http\Controllers\BiayaController;
use App\Http\Controllers\TagihanController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
| Di sinilah semua rute web aplikasi Anda didefinisikan.
|--------------------------------------------------------------------------
*/

// Halaman utama
Route::get('/', fn () => view('welcome'));

// Autentikasi default (login, register, dll)
Auth::routes();

// Home setelah login
Route::get('/home', [HomeController::class, 'index'])->name('home');

// ============================
// Rute untuk Operator
// ============================
Route::prefix('operator')
    ->middleware(['auth', 'auth.operator'])
    ->group(function () {
        Route::get('beranda', [BerandaOperatorController::class, 'index'])->name('operator.beranda');
        Route::resource('user', UserController::class);
        Route::resource('wali', WaliController::class);
        Route::resource('siswa', SiswaController::class);
        Route::resource('walisiswa', WaliSiswaController::class);
        Route::resource('biaya', BiayaController::class);
        Route::resource('tagihan', TagihanController::class);
    });

// ============================
// Rute untuk Wali
// ============================
Route::prefix('wali')
    ->middleware(['auth', 'auth.wali'])
    ->group(function () {
        Route::get('beranda', [BerandaWaliController::class, 'index'])->name('wali.beranda');
    });

// ============================
// Rute untuk Admin (belum diisi)
// ============================
Route::prefix('admin')
    ->middleware(['auth', 'auth.admin'])
    ->group(function () {
        // Tambahkan rute admin di sini
    });

// ============================
// Logout manual
// ============================
Route::get('logout', function () {
    Auth::logout();
    return redirect('login');
})->name('logout');

