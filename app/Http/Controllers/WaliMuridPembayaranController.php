<?php

namespace App\Http\Controllers;

use App\Models\Tagihan;
use App\Models\Pembayaran;
use App\Models\BankSekolah;
use App\Models\Bank;
use Illuminate\Http\Request;

class WaliMuridPembayaranController extends Controller
{
    /**
     * Menampilkan form konfirmasi pembayaran.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function create(Request $request)
    {
        $data['tagihan'] = Tagihan::where('id', $request->tagihan_id)->first();
        $data['bankSekolah'] = BankSekolah::findOrFail($request->bank_sekolah_id);
        $data['model'] = new Pembayaran();
        $data['method'] = "POST";
        $data['route'] = 'wali.pembayaran.store';
        $data['listBank'] = BankSekolah::pluck('nama_bank', 'id');

        return view('wali.pembayaran_form', $data);
    }


}
