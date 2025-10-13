<?php

namespace App\Http\Controllers;

use App\Models\Tagihan;
use App\Models\Pembayaran;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class BerandaWaliController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $userId = $user->id;

        // Jumlah anak (siswa yang dimiliki wali)
        $jumlahAnak = $user->siswa()->count();

        // Tagihan yang belum lunas (belum ada pembayaran dengan status 'sudah')
        $tagihanBelumLunas = Cache::remember("tagihan_belum_lunas_wali_{$userId}", now()->addMinutes(10), function () use ($user) {
            return Tagihan::waliSiswa()
                ->whereDoesntHave('pembayaran', function ($query) {
                    $query->where('status_konfirmasi', 'sudah');
                })
                ->count();
        });

        // Riwayat pembayaran terbaru (maks 5)
        $riwayatPembayaran = Cache::remember("riwayat_pembayaran_wali_{$userId}", now()->addMinutes(5), function () use ($user) {
            return Pembayaran::where('wali_id', $user->id)
                ->with('tagihan.siswa')
                ->orderBy('created_at', 'desc')
                ->limit(3)
                ->get();
        });

        return view('wali.beranda_index', compact(
            'jumlahAnak',
            'tagihanBelumLunas',
            'riwayatPembayaran'
        ));
    }
}
