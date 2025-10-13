<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Controllers
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WaliController;
use App\Http\Controllers\BiayaController;
use App\Http\Controllers\SiswaController;
use App\Http\Controllers\TagihanController;
use App\Http\Controllers\WaliSiswaController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\BankSekolahController;
use App\Http\Controllers\PembayaranController;
use App\Http\Controllers\BerandaWaliController;
use App\Http\Controllers\BerandaOperatorController;
use App\Http\Controllers\WaliMuridPembayaranController;
use App\Http\Controllers\WaliMuridProfileController;
use App\Http\Controllers\WaliMuridSiswaController;
use App\Http\Controllers\WaliMuridTagihanController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
| Semua rute web aplikasi didefinisikan di sini.
| File ini akan dimuat oleh RouteServiceProvider.
|--------------------------------------------------------------------------
*/

// ============================================================================
// Rute Publik
// ============================================================================
Route::get('/', fn () => view('welcome')); // Halaman landing page

// Rute autentikasi bawaan Laravel (login, register, password reset, dll)
Auth::routes();

// Halaman setelah login (default)
Route::get('/home', [HomeController::class, 'index'])->name('home');

// Login khusus wali
Route::get('login-wali', [LoginController::class, 'showLoginFormWali'])->name('login.wali');

// ============================================================================
// Rute Operator
// ============================================================================
Route::prefix('operator')
    ->middleware(['auth', 'auth.operator'])
    ->group(function () {
        Route::get('beranda', [BerandaOperatorController::class, 'index'])->name('operator.beranda');
        Route::put('/pembayaran/update-multiple', [PembayaranController::class, 'updateMultiple'])->name('pembayaran.update.multiple');

        // Resource routes
        Route::resource('user', UserController::class);
        Route::resource('wali', WaliController::class);
        Route::delete('siswa/mass-destroy', [SiswaController::class, 'massDestroy'])->name('siswa.massDestroy');
        Route::resource('siswa', SiswaController::class);
        Route::resource('walisiswa', WaliSiswaController::class);
        Route::resource('biaya', BiayaController::class);
        Route::resource('tagihan', TagihanController::class)->except(['edit', 'update']);
        Route::resource('pembayaran', PembayaranController::class);
        Route::resource('banksekolah', BankSekolahController::class);

        // Tambahan rute khusus
        Route::get('/tagihan/export', [TagihanController::class, 'export'])->name('tagihan.export');
        Route::delete('/tagihan/siswa/{siswa}', [TagihanController::class, 'destroySiswa'])->name('tagihan.destroySiswa');

        // Rute debug hanya di environment local (lebih aman)
        if (app()->environment('local')) {
            Route::get('/debug-tagihan', function () {
                $model = new \App\Models\Tagihan();
                $tableName = $model->getTable();
                $firstRecord = $model->first();

                return response()->json([
                    'message'         => 'Debug Model Tagihan',
                    'expected_table'  => 'tagihans',
                    'actual_table'    => $tableName,
                    'table_correct'   => $tableName === 'tagihans',
                    'has_records'     => $firstRecord !== null,
                    'first_record_id' => $firstRecord?->id,
                ]);
            });
        }
    });

// ============================================================================
// Rute Wali
// ============================================================================
Route::prefix('wali')
    ->middleware(['auth', 'auth.wali'])
    ->name('wali.')
    ->group(function () {
        Route::put('/pembayaran/update-multiple', [PembayaranController::class, 'updateMultiple'])->name('pembayaran.update.multiple');
        Route::get('beranda', [BerandaWaliController::class, 'index'])->name('beranda');
        Route::resource('siswa', WaliMuridSiswaController::class)->only(['index', 'show', 'edit', 'update']);
        Route::resource('tagihan', WaliMuridTagihanController::class);
        Route::resource('pembayaran', WaliMuridPembayaranController::class);
        Route::get('profile', [WaliMuridProfileController::class, 'index'])->name('profile.index');
        Route::get('profile/edit', [WaliMuridProfileController::class, 'edit'])->name('profile.edit');
        Route::put('profile/{id}', [WaliMuridProfileController::class, 'update'])->name('profile.update');
        Route::get('siswa/{siswa}/invoice', [WaliMuridTagihanController::class, 'cetakInvoice'])
            ->name('invoice.cetak');
    });

// ============================================================================
// Rute Admin (masih kosong, siap diisi)
// ============================================================================
Route::prefix('admin')
    ->middleware(['auth', 'auth.admin'])
    ->group(function () {
        // Tambahkan rute admin di sini
    });

// ============================================================================
// Logout Manual
// ============================================================================
Route::get('logout', function () {
    Auth::logout();
    return redirect('login');
})->name('logout');

Route::get('logoutwali', function () {
    Auth::logout();
    return redirect('login-wali');
})->name('logoutwali');
