<?php

namespace App\Http\Controllers;

use App\Models\Tagihan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WaliMuridTagihanController extends Controller
{
    public function index()
    {
        // Ambil semua ID siswa milik wali murid yang login
        $siswaIds = Auth::user()->siswa->pluck('id');

        // Ambil tagihan dengan pagination (10 per halaman)
        $tagihan = Tagihan::whereIn('siswa_id', $siswaIds)
            ->orderBy('tanggal_tagihan', 'desc') // bisa diubah sesuai kebutuhan
            ->paginate(10);

        return view('wali.tagihan_index', [
            'tagihan' => $tagihan
        ]);
    }
}
