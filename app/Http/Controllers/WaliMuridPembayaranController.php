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
        if ($request->wali_bank_id == '' && $request->nomor_rekening == '') {
            flash('Silakan pilih bank pengirim')->error();
            return back();
        }

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

        }

    } else {
        // Wali memilih rekening dari dropdown
        $waliBankId = $request->wali_bank_id;
        $waliBank = WaliBank::findOrFail($waliBankId);

        }
        $request->validate([
            'tanggal_bayar'   => 'required|date',
            'jumlah_dibayar'  => 'required|numeric',
            'bukti_bayar'     => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:5048',
        ]);

        // Simpan file bukti bayar ke storage
        $buktiBayar = $request->file('bukti_bayar')->store('public');

        // Siapkan data pembayaran
        $dataPembayaran = [
            'bank_sekolah_id'    => $request->bank_sekolah_id,
            'wali_bank_id'       => $waliBank->id,
            'tagihan_id'         => $request->tagihan_id,
            'wali_id'            => auth()->user()->id,
            'tanggal_bayar'      => $request->tanggal_bayar,
            'status_konfirmasi'  => 'belum',
            'jumlah_dibayar'     => str_replace('.', '', $request->jumlah_dibayar),
            'bukti_bayar'        => $buktiBayar,
            'metode_pembayaran'  => 'transfer',
            'user_id'            => 0,
        ];

        // Simpan data ke database
        Pembayaran::create($dataPembayaran);

        // Tampilkan pesan sukses
        flash('Pembayaran berhasil disimpan dan akan segera dikonfirmasi oleh operator')->success();

        // Kembali ke halaman sebelumnya
        return back();


            }


}
