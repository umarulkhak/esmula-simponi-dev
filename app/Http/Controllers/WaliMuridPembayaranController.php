<?php

namespace App\Http\Controllers;

use App\Models\Bank;
use App\Models\Tagihan;
use App\Models\WaliBank;
use App\Models\Pembayaran;
use App\Models\BankSekolah;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use App\Notifications\PembayaranNotification;

/**
 * Controller untuk manajemen pembayaran tagihan oleh wali murid.
 *
 * Menyediakan fungsi untuk menampilkan form konfirmasi pembayaran dan menyimpan
 * data pembayaran yang telah dilakukan oleh wali murid, termasuk:
 * - Pemilihan atau pembuatan rekening pengirim (bank wali),
 * - Upload bukti pembayaran,
 * - Validasi input,
 * - Notifikasi ke operator sekolah atas pembayaran baru.
 *
 * Semua aksi dilindungi oleh middleware autentikasi dan validasi input
 * menggunakan Laravel Request Validation.
 *
 * @author  Umar Ulkhak
 * @date    20 September 2025
 * @updated 29 September 2025 — Perbaikan bug nilai null pada kolom wali_bank_id di tabel pembayaran
 */

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
        $data['route'] = ['wali.pembayaran.store'];
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
            'bukti_bayar'     => 'required|mimes:jpeg,png,jpg,gif,svg,pdf|max:5048',
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
        //dd($dataPembayaran);
        $pembayaran = Pembayaran::create($dataPembayaran);
        $userOperator = User::where('akses', 'operator')->get();
        Notification::send($userOperator, new PembayaranNotification($pembayaran));


        // Tampilkan pesan sukses
        flash('Pembayaran berhasil disimpan dan akan segera dikonfirmasi oleh operator')->success();

        // Kembali ke halaman sebelumnya
        return back();

    }
}
