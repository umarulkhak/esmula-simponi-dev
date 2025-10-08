<?php

namespace App\Http\Controllers;

use App\Models\Tagihan;
use App\Models\Pembayaran;
use App\Models\BankSekolah;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class WaliMuridTagihanController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if (!$user->siswa || $user->siswa->isEmpty()) {
            return view('wali.tagihan_index', [
                'tagihanGrouped' => collect(),
            ])->with('info', 'Anda belum memiliki siswa terdaftar.');
        }

        $tagihan = Tagihan::waliSiswa()
            ->orderBy('tanggal_tagihan', 'desc')
            ->get();

        $tagihanGrouped = $tagihan->groupBy('siswa_id');

        return view('wali.tagihan_index', compact('tagihanGrouped'));
    }

    public function show($siswaId)
    {
        try {
            $user = Auth::user();
            $userSiswaIds = $user->getAllSiswaId();

            if (!in_array($siswaId, $userSiswaIds)) {
                abort(403, 'Anda tidak memiliki akses ke data ini.');
            }

            // ✅ Load SEMUA pembayaran (tanpa filter status_konfirmasi)
            $tagihanList = Tagihan::with([
                    'siswa',
                    'tagihanDetails',
                    'pembayaran' // 👈 Ambil SEMUA pembayaran
                ])
                ->waliSiswa()
                ->where('siswa_id', $siswaId)
                ->orderBy('tanggal_tagihan', 'desc')
                ->get();

            if ($tagihanList->isEmpty()) {
                return redirect()->route('wali.tagihan.index')
                    ->with('error', 'Tidak ada tagihan ditemukan untuk siswa ini.');
            }

            $siswa = $tagihanList->first()->siswa;
            $banksekolah = BankSekolah::all();
            $listbank = \App\Models\Bank::pluck('nama_bank', 'id');

            // Hitung total tagihan
            $grandTotal = $tagihanList->sum(fn($t) => $t->tagihanDetails->sum('jumlah_biaya'));

            // ✅ Hitung total yang SUDAH DIKONFIRMASI (status_konfirmasi = 'sudah')
            $totalDibayarDikonfirmasi = $tagihanList->sum(function ($tagihan) {
                return $tagihan->pembayaran
                    ->where('status_konfirmasi', 'sudah')
                    ->sum('jumlah_dibayar');
            });

            $statusGlobal = $this->determinePaymentStatus($grandTotal, $totalDibayarDikonfirmasi);
            $semuaDikonfirmasi = ($statusGlobal === 'lunas');

            return view('wali.tagihan_show', compact(
                'tagihanList',
                'siswa',
                'banksekolah',
                'grandTotal',
                'totalDibayarDikonfirmasi',
                'statusGlobal',
                'listbank',
                'semuaDikonfirmasi'
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
