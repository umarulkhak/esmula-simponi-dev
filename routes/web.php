<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| File ini mendefinisikan semua rute HTTP untuk aplikasi web SIMPONI.
| Rute dibagi berdasarkan akses pengguna:
|   - Publik: halaman login, redirect awal
|   - Wali Murid: akses terbatas ke data anak dan pembayaran
|   - Operator/Admin: akses penuh ke manajemen sistem
|
| Fitur utama:
|   ✅ Multi-role authentication (wali, operator, admin)
|   ✅ Redirect dinamis setelah login/logout berdasarkan peran
|   ✅ Perlindungan terhadap akses ilegal ke `/home`
|   ✅ Dukungan dua tampilan login berbeda (umum & wali)
|   ✅ Debug route hanya aktif di lingkungan lokal
|
| Penulis: Umar Ulkhak
| Versi: 1.0
| Terakhir diperbarui: 15 Oktober 2025
|
*/

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Daftar controller yang digunakan
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\BerandaOperatorController;
use App\Http\Controllers\BerandaWaliController;
use App\Http\Controllers\BiayaController;
use App\Http\Controllers\BankSekolahController;
use App\Http\Controllers\PembayaranController;
use App\Http\Controllers\SiswaController;
use App\Http\Controllers\TagihanController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WaliController;
use App\Http\Controllers\WaliSiswaController;
use App\Http\Controllers\WaliMuridPembayaranController;
use App\Http\Controllers\WaliMuridProfileController;
use App\Http\Controllers\WaliMuridSiswaController;
use App\Http\Controllers\WaliMuridTagihanController;

/*
|--------------------------------------------------------------------------
| RUTE PUBLIK (TIDAK MEMERLUKAN AUTENTIKASI)
|--------------------------------------------------------------------------
*/

// Redirect root ke halaman login khusus wali murid
Route::get('/', function () {
    return redirect()->route('login.wali');
})->name('welcome');

// Penanganan akses ke `/home` (default Laravel) — redirect sesuai peran
// Mencegah error 404 jika user mengetik `/home` secara manual
Route::get('/home', function () {
    if (auth()->check()) {
        $user = auth()->user();
        if ($user->akses === 'wali') {
            return redirect()->route('wali.beranda');
        } elseif (in_array($user->akses, ['operator', 'admin'])) {
            return redirect()->route('operator.beranda');
        }
    }
    return redirect()->route('login');
})->name('home');

// Rute autentikasi bawaan Laravel (login, logout, reset password)
// - Login: POST /login
// - Logout: POST /logout (diganti dengan custom GET di bawah)
// - Reset password: tersedia tapi tidak aktif secara default
Auth::routes();

// Halaman login khusus untuk wali murid (tampilan berbeda)
Route::get('login-wali', [LoginController::class, 'showLoginFormWali'])
    ->name('login.wali')
    ->middleware('guest');

/*
|--------------------------------------------------------------------------
| RUTE OPERATOR & ADMIN
|--------------------------------------------------------------------------
| Middleware:
|   - `auth`: user harus login
|   - `auth.operator`: hanya operator/admin yang diizinkan
*/
Route::prefix('operator')
    ->middleware(['auth', 'auth.operator'])
    ->group(function () {
        // Beranda operator
        Route::get('beranda', [BerandaOperatorController::class, 'index'])
            ->name('operator.beranda');

        // Update pembayaran massal
        Route::put('/pembayaran/update-multiple', [PembayaranController::class, 'updateMultiple'])
            ->name('pembayaran.update.multiple');

        // Aksi khusus
        Route::delete('siswa/mass-destroy', [SiswaController::class, 'massDestroy'])
            ->name('siswa.massDestroy');
        Route::delete('pembayaran/mass-destroy', [PembayaranController::class, 'massDestroy'])
            ->name('pembayaran.massDestroy');
        Route::post('/tagihan/{tagihan}/bayar', [TagihanController::class, 'bayar'])
            ->name('tagihan.bayar');
        Route::delete('/tagihan/siswa/{siswa}', [TagihanController::class, 'destroySiswa'])
            ->name('tagihan.destroySiswa');
        Route::get('/tagihan/export', [TagihanController::class, 'export'])
            ->name('tagihan.export');
        Route::delete('wali/bulk-destroy', [WaliController::class, 'bulkDestroy'])->name('wali.bulk-destroy');
        Route::delete('wali/destroy-all', [WaliController::class, 'destroyAll'])->name('wali.destroy-all');

        // Resource controllers
        Route::resource('user', UserController::class);
        Route::resource('wali', WaliController::class);
        Route::resource('siswa', SiswaController::class);
        Route::resource('walisiswa', WaliSiswaController::class);
        Route::resource('biaya', BiayaController::class);
        Route::resource('tagihan', TagihanController::class)->except(['edit', 'update']);
        Route::resource('pembayaran', PembayaranController::class);
        Route::resource('banksekolah', BankSekolahController::class);



        // Route debug (hanya aktif di local)
        if (app()->environment('local')) {
            Route::get('/debug-tagihan', function () {
                $model = new \App\Models\Tagihan();
                $firstRecord = $model->first();

                return response()->json([
                    'message'         => 'Debug Model Tagihan',
                    'expected_table'  => 'tagihans',
                    'actual_table'    => $model->getTable(),
                    'table_correct'   => $model->getTable() === 'tagihans',
                    'has_records'     => $firstRecord !== null,
                    'first_record_id' => $firstRecord?->id,
                ]);
            });
        }
    });

/*
|--------------------------------------------------------------------------
| RUTE WALI MURID
|--------------------------------------------------------------------------
| Middleware:
|   - `auth`: user harus login
|   - `auth.wali`: hanya wali murid yang diizinkan
*/
Route::prefix('wali')
    ->middleware(['auth', 'auth.wali'])
    ->name('wali.')
    ->group(function () {
        // Beranda wali
        Route::get('beranda', [BerandaWaliController::class, 'index'])
            ->name('beranda');

        // Update pembayaran massal
        Route::put('/pembayaran/update-multiple', [PembayaranController::class, 'updateMultiple'])
            ->name('pembayaran.update.multiple');

        // Resource controllers (terbatas)
        Route::resource('siswa', WaliMuridSiswaController::class)
            ->only(['index', 'show', 'edit', 'update']);
        Route::resource('tagihan', WaliMuridTagihanController::class);
        Route::resource('pembayaran', WaliMuridPembayaranController::class);

        // Profil wali
        Route::get('profile', [WaliMuridProfileController::class, 'index'])
            ->name('profile.index');
        Route::get('profile/edit', [WaliMuridProfileController::class, 'edit'])
            ->name('profile.edit');
        Route::put('profile/{id}', [WaliMuridProfileController::class, 'update'])
            ->name('profile.update');

        // Cetak invoice
        Route::get('siswa/{siswa}/invoice', [WaliMuridTagihanController::class, 'cetakInvoice'])
            ->name('invoice.cetak');
    });

/*
|--------------------------------------------------------------------------
| RUTE ADMIN (DASAR — BISA DIKEMBANGKAN)
|--------------------------------------------------------------------------
*/
Route::prefix('admin')
    ->middleware(['auth', 'auth.admin'])
    ->group(function () {
        Route::get('dashboard', function () {
            return view('admin.dashboard');
        })->name('admin.dashboard');
    });

/*
|--------------------------------------------------------------------------
| LOGOUT — SATU ROUTE CERDAS UNTUK SEMUA PERAN
|--------------------------------------------------------------------------
| Menghindari duplikasi route logout.
| Setelah logout, user diarahkan ke halaman login sesuai peran terakhir.
*/
Route::get('logout', function () {
    if (Auth::check()) {
        $akses = Auth::user()->akses;
        Auth::logout();

        // Redirect ke login sesuai peran
        if ($akses === 'wali') {
            return redirect()->route('login.wali');
        }
    }

    // Default: operator/admin
    return redirect()->route('login');
})->name('logout');

/*
|--------------------------------------------------------------------------
| CATATAN PENTING
|--------------------------------------------------------------------------
| 1. Jangan gunakan route `logoutwali` — sudah digantikan oleh route `logout`.
| 2. Semua form logout harus menggunakan `route('logout')` dengan method POST (via form).
| 3. Pastikan middleware `auth.operator` dan `auth.wali` memvalidasi peran dengan ketat.
| 4. Route `/home` sengaja dipertahankan untuk kompatibilitas dengan redirect default Laravel.
*/
