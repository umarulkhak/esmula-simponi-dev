<?php

namespace App\Http\Controllers;

use App\Models\Bank;
use App\Models\Tagihan;
use App\Models\WaliBank;
use App\Models\Pembayaran;
use App\Models\BankSekolah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        $data['tagihan'] = Tagihan::waliSiswa()->findOrFail($request->tagihan_id);
        $data['bankSekolah'] = BankSekolah::findOrFail($request->bank_sekolah_id);
        $data['model'] = new Pembayaran();
        $data['method'] = "POST";
        $data['route'] = 'wali.pembayaran.store';
        $data['listWaliBank'] = WaliBank::where('wali_id', Auth::user()->id)->get()->pluck('nama_bank_full', 'id');
        $data['listBankSekolah'] = BankSekolah::pluck('nama_bank', 'id');
        $data['listBank'] = Bank::pluck('nama_bank', 'id');
        if (!empty($request->bank_sekolah_id)) {
            $data['bankYangDipilih'] = BankSekolah::findOrFail($request->bank_sekolah_id);
        }
        $data['url'] = route('wali.pembayaran.create', [
            'tagihan_id' => $request->tagihan_id,
        ]);



        return view('wali.pembayaran_form', $data);
    }

    public function store(Request $request)
    {
       if ($request->filled('pilihan_bank')) {
        // Wali membuat rekening baru
        $bankId = $request->bank_id;
        $bank = Bank::findOrFail($bankId);
        if ($request->filled('simpan_data_rekening')) {
            $requestDataBank = $request->validate([
                'nama_rekening' => 'required',
                'nomor_rekening' => 'required',
            ]);
            $waliBank = WaliBank::firstOrCreate(
                $requestDataBank,
                [
                'nama_rekening' => $requestDataBank['nama_rekening'],
                'wali_id' => Auth::user()->id,
                'kode' => $bank->sandi_bank,
                'nama_bank' => $bank->nama_bank,
            ]
        );

            dd($waliBank);
        }

    } else {
        // Wali memilih rekening dari dropdown
        $waliBankId = $request->wali_bank_id;
        $waliBank = WaliBank::findOrFail($waliBankId);

        }
        dd($waliBankId);
    }




}
