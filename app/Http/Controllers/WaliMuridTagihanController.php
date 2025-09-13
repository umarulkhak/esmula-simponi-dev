<?php

namespace App\Http\Controllers;

use App\Models\Tagihan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WaliMuridTagihanController extends Controller
{
    public function index()
    {
        $siswald = Auth::user()->siswa->pluck('id');
        $tagihan = Tagihan::whereIn('siswa_id', $siswald)->get();
        dd($tagihan);


    }

}
