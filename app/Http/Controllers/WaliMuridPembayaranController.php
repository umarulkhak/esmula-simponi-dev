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
    // Simpan pembayaran dari modal via AJAX
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tagihan_id' => 'required|exists:tagihans,id',
            'bank_id' => 'required|exists:bank_sekolahs,id',
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

            // Simpan data pembayaran
            $pembayaran = Pembayaran::create([
                'tagihan_id' => $request->tagihan_id,
                'bank_id' => $request->bank_id,
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
            return response()->json(['error' => 'Gagal menyimpan pembayaran.'], 500);
        }
    }
}
