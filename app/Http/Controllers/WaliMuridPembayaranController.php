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
 * - Pemilihan rekening tersimpan atau input rekening baru,
 * - Opsi menyimpan rekening baru ke daftar pribadi (opsional),
 * - Upload bukti pembayaran,
 * - Validasi input secara ketat,
 * - Penyimpanan lengkap data rekening pengirim (bahkan jika tidak disimpan ke wali_banks),
 * - Notifikasi real-time ke operator sekolah.
 *
 * Semua aksi dilindungi oleh middleware autentikasi dan menggunakan validasi Laravel
 * untuk memastikan integritas data. Solusi ini aman untuk:
 * - User baru pertama kali (tanpa rekening tersimpan),
 * - User lama yang memilih dari daftar atau input rekening baru,
 * - User yang memilih untuk menyimpan atau tidak menyimpan data rekening.
 *
 * @author  Umar Ulkhak
 * @date    20 September 2025
 * @updated 29 September 2025 — Perbaikan bug nilai null pada kolom wali_bank_id di tabel pembayaran
 * @updated 29 September 2025 — Penyimpanan eksplisit data rekening pengirim ke tabel pembayaran
 *                              untuk menjamin ketersediaan data verifikasi operator
 * @updated 01 Oktober 2025   — Penambahan validasi duplikat pembayaran berdasarkan jumlah, tagihan,
 *                              dan status konfirmasi (untuk menghindari data ganda)
 */
class WaliMuridPembayaranController extends Controller
{
    /**
     * Menampilkan form konfirmasi pembayaran.
     *
     * Mengambil data tagihan, bank sekolah tujuan, dan daftar rekening wali yang tersimpan
     * untuk ditampilkan dalam form. Mendukung prefill bank tujuan berdasarkan query string.
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
        $data['listWaliBank'] = WaliBank::where('wali_id', Auth::id())->get()->pluck('nama_bank_full', 'id');
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

    /**
     * Menyimpan data pembayaran baru dari wali murid.
     *
     * Logika utama:
     * - Jika `wali_bank_id` terisi → ambil data dari tabel `wali_banks`.
     * - Jika tidak → ambil data dari input form (`bank_id`, `nama_rekening`, `nomor_rekening`).
     * - Jika checkbox "simpan_data_rekening" dicentang → simpan ke `wali_banks`.
     * - Simpan SEMUA data rekening pengirim ke tabel `pembayarans` (untuk verifikasi operator).
     * - Kirim notifikasi ke semua user dengan akses 'operator'.
     * - Validasi duplikat pembayaran: mencegah data ganda dengan kriteria
     *   `jumlah_dibayar`, `tagihan_id`, dan `status_konfirmasi = belum`.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // Validasi awal: minimal salah satu cara input rekening harus ada
        if (!$request->filled('wali_bank_id') && !$request->filled('nomor_rekening')) {
            flash('Silakan pilih bank pengirim atau isi data rekening baru')->error();
            return back()->withInput();
        }

        // Validasi utama pembayaran
        $request->validate([
            'tanggal_bayar'   => 'required|date',
            'jumlah_dibayar'  => 'required|numeric',
            'bukti_bayar'     => 'required|mimes:jpeg,png,jpg,gif,svg,pdf|max:5048',
        ]);

        // Validasi tambahan jika input rekening baru
        if (!$request->filled('wali_bank_id')) {
            $request->validate([
                'bank_id'         => 'required|exists:banks,id',
                'nama_rekening'   => 'required|string|max:255',
                'nomor_rekening'  => 'required|string|max:50',
            ]);
        }

        /**
         * ============================
         * VALIDASI DUPLIKAT PEMBAYARAN
         * ============================
         * Mencegah data ganda untuk pembayaran dengan:
         * - jumlah_dibayar sama,
         * - tagihan_id sama,
         * - status_konfirmasi masih 'belum'.
         */
        $jumlahDibayar = str_replace(['.', '•'], '', $request->jumlah_dibayar);

        $validasiPembayaran = Pembayaran::where('jumlah_dibayar', $jumlahDibayar)
            ->where('tagihan_id', $request->tagihan_id)
            ->where('status_konfirmasi', 'belum')
            ->exists();

        if ($validasiPembayaran) {
            flash('Data pembayaran ini sudah ada dan sedang menunggu konfirmasi operator')->error();
            return back()->withInput();
        }

        // === Ambil data rekening pengirim ===
        if ($request->filled('wali_bank_id')) {
            // User memilih dari daftar rekening tersimpan
            $waliBank = WaliBank::findOrFail($request->wali_bank_id);
            $namaRek = $waliBank->nama_rekening;
            $nomorRek = $waliBank->nomor_rekening;
            $namaBank = $waliBank->nama_bank;
            $kodeBank = $waliBank->kode;
            $waliBankId = $waliBank->id;
        } else {
            // User mengisi rekening baru
            $bank = Bank::findOrFail($request->bank_id);
            $namaRek = $request->nama_rekening;
            $nomorRek = $request->nomor_rekening;
            $namaBank = $bank->nama_bank;
            $kodeBank = $bank->sandi_bank;

            // Simpan ke wali_banks hanya jika dicentang
            if ($request->filled('simpan_data_rekening')) {
                $waliBank = WaliBank::firstOrCreate(
                    [
                        'wali_id' => Auth::id(),
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
                $waliBankId = null; // Tidak disimpan ke wali_banks
            }
        }

        // Simpan bukti pembayaran ke storage
        $buktiBayar = $request->file('bukti_bayar')->store('public');

        // Simpan data pembayaran lengkap (termasuk info rekening pengirim)
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

        // Kirim notifikasi ke operator
        $operatorUsers = User::where('akses', 'operator')->get();
        Notification::send($operatorUsers, new PembayaranNotification($pembayaran));

        flash('Pembayaran berhasil disimpan dan akan segera dikonfirmasi oleh operator')->success();
        return back();
    }
}
