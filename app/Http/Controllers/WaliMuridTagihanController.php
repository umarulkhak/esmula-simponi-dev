<?php

namespace App\Http\Controllers;

use App\Models\BankSekolah;
use App\Models\Tagihan;
use App\Models\Pembayaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class WaliMuridTagihanController extends Controller
{
    /**
     * Menampilkan daftar tagihan SPP untuk semua siswa milik wali yang login.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $user = Auth::user();

        // Pastikan wali memiliki siswa
        if (!$user->siswa || $user->siswa->isEmpty()) {
            return view('wali.tagihan_index', [
                'tagihanGrouped' => collect(),
            ])->with('info', 'Anda belum memiliki siswa terdaftar.');
        }

        // Ambil ID siswa yang dimiliki wali
        $siswaIds = $user->siswa->pluck('id');

        // Load tagihan beserta relasi siswa dan rincian biaya
        $tagihan = Tagihan::with(['siswa', 'tagihanDetails'])
            ->whereIn('siswa_id', $siswaIds)
            ->orderBy('tanggal_tagihan', 'desc')
            ->get();

        // Kelompokkan tagihan berdasarkan siswa
        $tagihanGrouped = $tagihan->groupBy('siswa_id');

        return view('wali.tagihan_index', compact('tagihanGrouped'));
    }

    /**
     * Menampilkan detail tagihan beserta rincian biaya dan riwayat pembayaran untuk satu siswa.
     *
     * @param  int  $siswaId
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function show($siswaId)
    {
        try {
            $user = Auth::user();
            $userSiswaIds = $user->siswa->pluck('id');

            // Verifikasi akses: pastikan siswa milik wali
            if (!$userSiswaIds->contains($siswaId)) {
                abort(403, 'Anda tidak memiliki akses ke data ini.');
            }

            // Ambil semua tagihan untuk siswa ini beserta relasi
            $tagihanList = Tagihan::with(['siswa', 'tagihanDetails'])
                ->where('siswa_id', $siswaId)
                ->orderBy('tanggal_tagihan', 'desc')
                ->get();

            // Pastikan siswa ditemukan
            $siswa = $tagihanList->first()?->siswa;
            if (!$siswa) {
                return redirect()->route('wali.tagihan.index')
                    ->with('error', 'Siswa tidak ditemukan.');
            }

            // Ambil riwayat pembayaran untuk tagihan siswa ini
            $pembayaran = Pembayaran::whereIn('tagihan_id', $tagihanList->pluck('id'))
                ->orderBy('tanggal_bayar', 'desc')
                ->get();

            // Ambil daftar rekening bank sekolah
            $banksekolah = BankSekolah::all();

            // Hitung total tagihan & total yang sudah dibayar
            $grandTotal = $tagihanList->sum(fn($tagihan) => $tagihan->tagihanDetails->sum('jumlah_biaya'));
            $totalDibayar = $pembayaran->sum('jumlah_dibayar');

            // Tentukan status global pembayaran
            $statusGlobal = $this->determinePaymentStatus($grandTotal, $totalDibayar);

            // Kirim data ke view
            return view('wali.tagihan_show', compact(
                'tagihanList',
                'siswa',
                'pembayaran',
                'banksekolah',
                'grandTotal',
                'totalDibayar',
                'statusGlobal'
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
     * @param  float  $grandTotal
     * @param  float  $totalDibayar
     * @return string
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
