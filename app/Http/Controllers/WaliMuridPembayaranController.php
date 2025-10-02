<?php

namespace App\Http\Controllers;

use App\Models\Bank;
use App\Models\User;
use App\Models\Tagihan;
use App\Models\WaliBank;
use App\Models\Pembayaran;
use App\Models\BankSekolah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use App\Notifications\PembayaranNotification;

/**
 * Controller untuk manajemen pembayaran tagihan oleh wali murid.
 *
 * Fitur utama:
 * - Menampilkan form konfirmasi pembayaran
 * - Validasi input pembayaran
 * - Menyimpan rekening baru (opsional)
 * - Mencegah duplikat pembayaran
 * - Upload bukti pembayaran
 * - Simpan semua detail rekening pengirim ke tabel `pembayarans`
 * - Mengirim notifikasi ke operator sekolah
 *
 * @author  Umar Ulkhak
 * @date    20 September 2025
 * @updated 29 September 2025 — Fix kolom wali_bank_id null
 * @updated 01 Oktober 2025   — Tambah validasi duplikat pembayaran
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
        $data['method'] = 'POST';
        $data['route'] = ['wali.pembayaran.store'];

        // Daftar rekening wali yang sudah tersimpan
        $data['listWaliBank'] = WaliBank::where('wali_id', Auth::id())
            ->get()
            ->pluck('nama_bank_full', 'id');

        // Daftar bank sekolah & bank umum
        $data['listBankSekolah'] = BankSekolah::pluck('nama_bank', 'id');
        $data['listBank'] = Bank::pluck('nama_bank', 'id');

        // Prefill bank tujuan jika ada di query string
        if (!empty($request->bank_sekolah_id)) {
            $data['bankYangDipilih'] = BankSekolah::findOrFail($request->bank_sekolah_id);
        }

        // URL form (agar bisa refresh tanpa kehilangan data)
        $data['url'] = route('wali.pembayaran.create', [
            'tagihan_id' => $request->tagihan_id,
        ]);

        return view('wali.pembayaran_form', $data);
    }

    /**
     * Menyimpan data pembayaran baru dari wali murid.
     *
     * Alur proses:
     * 1. Validasi input wajib (tanggal, jumlah, bukti).
     * 2. Cek apakah rekening dipilih dari daftar wali atau diinput baru.
     * 3. Jika rekening baru & "simpan" dicentang → simpan ke tabel `wali_banks`.
     * 4. Cegah duplikat pembayaran (jumlah, tagihan, status_konfirmasi=belum).
     * 5. Simpan data pembayaran lengkap (termasuk info rekening pengirim).
     * 6. Kirim notifikasi ke operator sekolah.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        /**
         * ===============================
         *  VALIDASI INPUT
         * ===============================
         */
        // Minimal salah satu cara input rekening harus ada
        if (!$request->filled('wali_bank_id') && !$request->filled('nomor_rekening')) {
            flash('Silakan pilih bank pengirim atau isi data rekening baru')->error();
            return back()->withInput();
        }

        // Validasi utama
        $request->validate([
            'tanggal_bayar'   => 'required|date',
            'jumlah_dibayar'  => 'required|numeric',
            'bukti_bayar'     => 'required|mimes:jpeg,png,jpg,gif,svg,pdf|max:5048',
        ]);

        // Validasi tambahan jika rekening baru
        if (!$request->filled('wali_bank_id')) {
            $request->validate([
                'bank_id'         => 'required|exists:banks,id',
                'nama_rekening'   => 'required|string|max:255',
                'nomor_rekening'  => 'required|string|max:50',
            ]);
        }

        /**
         * ===============================
         *  VALIDASI DUPLIKAT PEMBAYARAN
         * ===============================
         */
        $jumlahDibayar = (int) str_replace(['.', '•'], '', $request->jumlah_dibayar);

        $validasiPembayaran = Pembayaran::where('jumlah_dibayar', $jumlahDibayar)
            ->where('tagihan_id', $request->tagihan_id)
            ->where('status_konfirmasi', 'belum')
            ->exists();

        if ($validasiPembayaran) {
            flash('Data pembayaran serupa sudah tercatat dan masih menunggu konfirmasi operator.')->error();
            return back()->withInput();
        }

        /**
         * ===============================
         *  AMBIL DATA REKENING PENGIRIM
         * ===============================
         */
        if ($request->filled('wali_bank_id')) {
            // Rekening dipilih dari daftar wali
            $waliBank = WaliBank::findOrFail($request->wali_bank_id);

            $namaRek   = $waliBank->nama_rekening;
            $nomorRek  = $waliBank->nomor_rekening;
            $namaBank  = $waliBank->nama_bank;
            $kodeBank  = $waliBank->kode;
            $waliBankId = $waliBank->id;

        } else {
            // Rekening baru diinput manual
            $bank = Bank::findOrFail($request->bank_id);

            $namaRek   = $request->nama_rekening;
            $nomorRek  = $request->nomor_rekening;
            $namaBank  = $bank->nama_bank;
            $kodeBank  = $bank->sandi_bank;

            // Simpan ke tabel wali_banks hanya jika dicentang
            if ($request->filled('simpan_data_rekening')) {
                $waliBank = WaliBank::firstOrCreate(
                    [
                        'wali_id'        => Auth::id(),
                        'nomor_rekening' => $nomorRek,
                    ],
                    [
                        'nama_rekening' => $namaRek,
                        'nama_bank'     => $namaBank,
                        'kode'          => $kodeBank,
                    ]
                );
                $waliBankId = $waliBank->id;
            } else {
                $waliBankId = null;
            }
        }

        /**
         * ===============================
         *  SIMPAN DATA PEMBAYARAN
         * ===============================
         */
        $buktiBayar = $request->file('bukti_bayar')->store('public');

        DB::beginTransaction();
        try {
            // Buat data pembayaran baru
            $pembayaran = Pembayaran::create([
                'tagihan_id'              => $request->tagihan_id,
                'wali_id'                 => Auth::id(),
                'wali_bank_id'            => $waliBankId,
                'nama_rekening_pengirim'  => $namaRek,
                'nomor_rekening_pengirim' => $nomorRek,
                'nama_bank_pengirim'      => $namaBank,
                'kode_bank_pengirim'      => $kodeBank,
                'bank_sekolah_id'         => $request->bank_sekolah_id,
                'tanggal_bayar'           => $request->tanggal_bayar,
                'status_konfirmasi'       => 'belum',
                'jumlah_dibayar'          => $jumlahDibayar,
                'bukti_bayar'             => $buktiBayar,
                'metode_pembayaran'       => 'transfer',
                'user_id'                 => 0,
            ]);

            // Kirim notifikasi ke semua user operator
            $userOperator = User::where('akses', 'operator')->get();
            Notification::send($userOperator, new PembayaranNotification($pembayaran));

            DB::commit();

            flash('Berhasil! Pembayaran telah tercatat dan akan segera diverifikasi oleh operator sekolah.')->success();
            return back();

        } catch (\Throwable $th) {
            DB::rollBack();

            flash('Gagal menyimpan data pembayaran: ' . $th->getMessage())->error();
            return back();
        }
    }
}
