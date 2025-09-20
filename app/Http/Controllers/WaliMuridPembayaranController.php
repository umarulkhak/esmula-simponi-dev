<?php

namespace App\Http\Controllers;

use App\Models\Tagihan;
use App\Models\Pembayaran;
use App\Models\BankSekolah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class WaliMuridPembayaranController extends Controller
{
    /**
     * Simpan pembayaran dari modal via AJAX.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tagihan_id' => 'required|exists:tagihans,id',
            'bank_id' => 'required|exists:bank_sekolahs,id',
            'bank_pengirim_id' => 'required|exists:banks,id', // 👈 validasi bank pengirim
            'no_rekening_pengirim' => 'required|string|max:50', // 👈 validasi no rekening
            'nama_pengirim' => 'required|string|max:100',       // 👈 validasi nama pengirim
            'tanggal_bayar' => 'required|date',
            'jumlah_dibayar' => 'required|numeric|min:1',
            'bukti_bayar' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();
        try {
            // Upload file bukti bayar
            $buktiPath = $request->file('bukti_bayar')->store('bukti_pembayaran', 'public');

            // Simpan data pembayaran — termasuk field baru
            $pembayaran = Pembayaran::create([
                'tagihan_id' => $request->tagihan_id,
                'bank_id' => $request->bank_id,
                'bank_pengirim_id' => $request->bank_pengirim_id,     // 👈 simpan
                'no_rekening_pengirim' => $request->no_rekening_pengirim, // 👈 simpan
                'nama_pengirim' => $request->nama_pengirim,             // 👈 simpan
                'tanggal_bayar' => $request->tanggal_bayar,
                'jumlah_dibayar' => $request->jumlah_dibayar,
                'bukti_bayar' => $buktiPath,
            ]);

            DB::commit();

            // Ambil data tagihan terbaru untuk update frontend
            $tagihan = Tagihan::with('pembayaran')->find($request->tagihan_id);

            return response()->json([
                'success' => true,
                'message' => 'Pembayaran berhasil dikonfirmasi.',
                'pembayaran' => $pembayaran,
                'tagihan' => $tagihan,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Gagal menyimpan pembayaran: ' . $e->getMessage());
            return response()->json(['error' => 'Gagal menyimpan pembayaran.'], 500);
        }
    }
}
