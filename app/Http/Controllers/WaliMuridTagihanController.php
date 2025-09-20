<?php

namespace App\Http\Controllers;

use App\Models\BankSekolah;
use App\Models\Tagihan;
use App\Models\Pembayaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

/**
 * Controller untuk manajemen tagihan SPP wali murid.
 *
 * Menampilkan daftar tagihan per siswa dan detail pembayaran.
 * Dilengkapi proteksi akses: wali hanya bisa melihat data siswa yang dimilikinya.
 * Memanfaatkan scope `waliSiswa()` dari model Tagihan untuk konsistensi query.
 * Menyediakan daftar bank umum (\App\Models\Bank) untuk pilihan "Bank Pengirim".
 *
 * @author  Umar Ulkhak
 * @date    3 Agustus 2025
 * @updated 5 April 2025 — Tambah support bank pengirim dari model Bank, clean code, dokumentasi
 */
class WaliMuridTagihanController extends Controller
{
    /**
     * Menampilkan daftar tagihan SPP untuk semua siswa milik wali yang login.
     *
     * Menggunakan scope `waliSiswa()` untuk memastikan hanya menampilkan tagihan siswa milik user.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $user = Auth::user();

        // Cek apakah wali memiliki siswa terdaftar
        if (!$user->siswa || $user->siswa->isEmpty()) {
            return view('wali.tagihan_index', [
                'tagihanGrouped' => collect(),
            ])->with('info', 'Anda belum memiliki siswa terdaftar.');
        }

        // Gunakan scope `waliSiswa()` untuk ambil tagihan milik siswa user
        $tagihan = Tagihan::waliSiswa()
            ->orderBy('tanggal_tagihan', 'desc')
            ->get();

        // Kelompokkan tagihan berdasarkan siswa
        $tagihanGrouped = $tagihan->groupBy('siswa_id');

        return view('wali.tagihan_index', compact('tagihanGrouped'));
    }

    /**
     * Menampilkan detail tagihan dan riwayat pembayaran untuk satu siswa.
     *
     * Memvalidasi bahwa siswa yang diakses benar-benar dimiliki oleh wali yang sedang login.
     * Menyediakan data bank umum untuk dropdown "Bank Pengirim" di form pembayaran.
     *
     * @param  int  $siswaId  ID siswa yang ingin dilihat tagihannya
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function show($siswaId)
    {
        try {
            $user = Auth::user();
            $userSiswaIds = $user->getAllSiswaId();

            // 🔒 Proteksi akses: cegah manipulasi URL
            if (!in_array($siswaId, $userSiswaIds)) {
                abort(403, 'Anda tidak memiliki akses ke data ini.');
            }

            // Ambil tagihan siswa ini
            $tagihanList = Tagihan::waliSiswa()
                ->where('siswa_id', $siswaId)
                ->orderBy('tanggal_tagihan', 'desc')
                ->get();

            // Pastikan data siswa tersedia
            $siswa = $tagihanList->first()?->siswa;
            if (!$siswa) {
                return redirect()->route('wali.tagihan.index')
                    ->with('error', 'Siswa tidak ditemukan.');
            }

            // Ambil riwayat pembayaran
            $pembayaran = Pembayaran::whereIn('tagihan_id', $tagihanList->pluck('id'))
                ->orderBy('tanggal_bayar', 'desc')
                ->get();

            // 💰 Ambil daftar rekening bank sekolah (untuk BANK TUJUAN)
            $banksekolah = BankSekolah::all();

            // 💳 Ambil daftar bank umum (untuk BANK PENGIRIM di form pembayaran)
            $listbank = \App\Models\Bank::pluck('nama_bank', 'id');

            // Hitung total tagihan dan total pembayaran
            $grandTotal = $tagihanList->sum(fn($tagihan) => $tagihan->tagihanDetails->sum('jumlah_biaya'));
            $totalDibayar = $pembayaran->sum('jumlah_dibayar');

            // Tentukan status pembayaran global
            $statusGlobal = $this->determinePaymentStatus($grandTotal, $totalDibayar);

            // Kirim semua data ke view
            return view('wali.tagihan_show', compact(
                'tagihanList',
                'siswa',
                'pembayaran',
                'banksekolah',
                'grandTotal',
                'totalDibayar',
                'statusGlobal',
                'listbank' // 👈 Dikirim ke view untuk dropdown bank pengirim
            ));

        } catch (\Exception $e) {
            Log::error('Error loading tagihan siswa: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'siswa_id' => $siswaId,
                'exception' => $e,
            ]);

            return redirect()->route('wali.tagihan.index')
                ->with('error', 'Terjadi kesalahan saat memuat data. Silakan coba lagi.');
        }
    }

    /**
     * Menentukan status pembayaran global berdasarkan total tagihan dan pembayaran.
     *
     * @param  float  $grandTotal    Total seluruh tagihan
     * @param  float  $totalDibayar  Total yang sudah dibayar
     * @return string                Status: 'lunas', 'angsur', atau 'belum_bayar'
     */
    private function determinePaymentStatus(float $grandTotal, float $totalDibayar): string
    {
        if ($totalDibayar >= $grandTotal) {
            return 'lunas';
        } elseif ($totalDibayar > 0) {
            return 'angsur';
        }

        return 'belum_bayar';
    }
}
