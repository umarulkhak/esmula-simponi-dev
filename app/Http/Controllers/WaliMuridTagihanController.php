<?php

namespace App\Http\Controllers;

use App\Models\Tagihan;
use App\Models\Pembayaran; // Tambahkan ini jika ingin tampilkan riwayat pembayaran
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class WaliMuridTagihanController extends Controller
{
    /**
     * Menampilkan daftar tagihan SPP untuk semua siswa milik wali yang login.
     */
    public function index()
    {
        $user = Auth::user();
        if (!$user->siswa || $user->siswa->isEmpty()) {
            return view('wali.tagihan_index', [
                'tagihanGrouped' => collect(),
            ])->with('info', 'Anda belum memiliki siswa terdaftar.');
        }

        $siswaIds = $user->siswa->pluck('id');

        // ✅ Tambahkan 'tagihanDetails' di with()
        $tagihan = Tagihan::with(['siswa', 'tagihanDetails'])
            ->whereIn('siswa_id', $siswaIds)
            ->orderBy('tanggal_tagihan', 'desc')
            ->get();

        $tagihanGrouped = $tagihan->groupBy('siswa_id');

        return view('wali.tagihan_index', [
            'tagihanGrouped' => $tagihanGrouped,
        ]);
    }

    /**
     * Menampilkan detail tagihan beserta rincian biaya dan riwayat pembayaran.
     */
    /**
 * Menampilkan semua tagihan untuk satu siswa (bukan satu tagihan)
 */
    public function show($siswaId)
    {
        try {
            // Verifikasi: Pastikan siswa ini milik wali yang login
            $userSiswaIds = Auth::user()->siswa->pluck('id');
            if (!$userSiswaIds->contains($siswaId)) {
                abort(403, 'Anda tidak memiliki akses ke data ini.');
            }

            // Ambil semua tagihan untuk siswa ini
            $tagihanList = Tagihan::with(['siswa', 'tagihanDetails'])
                ->where('siswa_id', $siswaId)
                ->orderBy('tanggal_tagihan', 'desc')
                ->get();

            $siswa = $tagihanList->first()?->siswa;

            if (!$siswa) {
                return redirect()->route('wali.tagihan.index')->with('error', 'Siswa tidak ditemukan.');
            }

            // Opsional: Ambil riwayat pembayaran semua tagihan siswa ini
            $pembayaran = Pembayaran::whereIn('tagihan_id', $tagihanList->pluck('id'))
                ->orderBy('tanggal_bayar', 'desc')
                ->get();

            return view('wali.tagihan_show', [
                'tagihanList' => $tagihanList, // <-- sekarang LIST, bukan single
                'siswa'       => $siswa,
                'pembayaran'  => $pembayaran,
            ]);

        } catch (\Exception $e) {
            Log::error('Error loading tagihan siswa: ' . $e->getMessage());
            return redirect()->route('wali.tagihan.index')->with('error', 'Terjadi kesalahan saat memuat data.');
        }
    }
}
