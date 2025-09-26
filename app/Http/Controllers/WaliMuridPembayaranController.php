<?php

namespace App\Http\Controllers;

use App\Models\Tagihan;
use App\Models\Pembayaran;
use App\Models\BankSekolah;
use App\Models\Bank;
use App\Models\WaliBank; // Jika Anda punya model WaliBank
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

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
        $tagihanId = $request->query('tagihan_id');
        $bankId = $request->query('bank_id');

        // Validasi parameter
        if (!$tagihanId || !$bankId) {
            return redirect()->back()->with('error', 'Data tagihan atau bank tidak valid.');
        }

        // Ambil data dari database
        $tagihan = Tagihan::findOrFail($tagihanId);
        $bankTujuan = BankSekolah::findOrFail($bankId);

        // Ambil data bank tersimpan milik wali (jika ada)
        $waliBanks = auth()->user()->waliBanks ?? collect();

        // Ambil daftar bank pengirim (untuk dropdown)
        $listbank = Bank::all();

        return view('wali.pembayaran_form', [
            'model' => new Pembayaran(),
            'route' => 'wali.pembayaran.store', // <- pakai nama route, bukan route()
            'method' => 'POST',
            'tagihan' => $tagihan,
            'bankTujuan' => $bankTujuan,
            'waliBanks' => $waliBanks,
            'listbank' => $listbank,
        ]);

    }

    /**
     * Menyimpan data konfirmasi pembayaran.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tagihan_id' => 'required|exists:tagihans,id',
            'bank_id' => 'required|exists:bank_sekolahs,id',
            'bank_pengirim_id' => 'required|exists:banks,id',
            'no_rekening_pengirim' => 'required|string|max:50',
            'nama_pengirim' => 'required|string|max:100',
            'tanggal_bayar' => 'required|date',
            'jumlah_dibayar' => 'required|numeric|min:1',
            'bukti_bayar' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();
        try {
            // Simpan file bukti bayar
            $filePath = $request->file('bukti_bayar')->store('bukti_bayar', 'public');

            // Siapkan data pembayaran
            $dataPembayaran = [
                'bank_sekolah_id' => $request->bank_id,
                'wali_bank_id' => $request->bank_pengirim_id,
                'tagihan_id' => $request->tagihan_id,
                'wali_id' => auth()->user()->id,
                'tanggal_bayar' => $request->tanggal_bayar,
                'status_konfirmasi' => 'belum',
                'jumlah_dibayar' => $request->jumlah_dibayar,
                'bukti_bayar' => $filePath,
                'methode_pembayaran' => 'transfer',
                'user_id' => 0,
                'no_rekening_pengirim' => $request->no_rekening_pengirim,
                'nama_pengirim' => $request->nama_pengirim,
            ];

            $pembayaran = Pembayaran::create($dataPembayaran);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pembayaran berhasil dikirim.',
                'redirect' => route('wali.tagihan.show', $request->tagihan_id), // redirect ke detail tagihan
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Gagal menyimpan pembayaran: ' . $e->getMessage());
            return response()->json(['error' => 'Gagal menyimpan pembayaran.'], 500);
        }
    }
}
