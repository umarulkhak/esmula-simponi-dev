<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WaliMuridSiswaController extends Controller
{
    public function index()
    {
        // Ambil data siswa yang dimiliki wali ini
        $models = Auth::user()->siswa()
            ->with(['wali', 'user']) // eager load relasi supaya hemat query
            ->paginate(10); // pakai pagination supaya firstItem() dkk bekerja

        return view('wali.siswa_index', [
            'models' => $models,
            'routePrefix' => 'wali.siswa',
        ]);
    }

}
