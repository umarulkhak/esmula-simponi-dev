<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePembayaranRequest;
use App\Models\Pembayaran;
use App\Models\Tagihan;
use Illuminate\Support\Facades\DB;
use Request;

class PembayaranController extends Controller
{
    /**
     * Menyimpan pembayaran dan mengalokasikannya ke tagihan yang paling sesuai.
     *
     * - Jika ada tagihan dengan nilai SAMA → bayar lunas tagihan itu.
     * - Jika tidak ada → bayar tagihan terkecil yang bisa dibayar.
     * - Tampilkan pesan sukses spesifik.
     */

    public function index()
    {
        // Ambil ID pembayaran terbaru per siswa (melalui tagihan)
        $latestPaymentIds = DB::table('pembayarans')
            ->join('tagihans', 'pembayarans.tagihan_id', '=', 'tagihans.id')
            ->select(DB::raw('MAX(pembayarans.id) as id'))
            ->groupBy('tagihans.siswa_id');

        // Ambil data pembayaran berdasarkan ID tersebut, dengan relasi
        $models = Pembayaran::whereIn('id', $latestPaymentIds)
            ->with(['tagihan.siswa', 'wali'])
            ->orderBy('tanggal_konfirmasi', 'desc')
            ->paginate(50);

        return view('operator.pembayaran_index', compact('models'));
    }

    public function store(StorePembayaranRequest $request)
    {
        $requestData = $request->validated();
        // Ambil siswa_id dari tagihan
        $tagihanUtama = Tagihan::findOrFail($requestData['tagihan_id']);
        $siswaId = $tagihanUtama->siswa_id;
        $requestData['wali_id'] = $tagihanUtama->siswa->wali_id;

        // Ambil SEMUA tagihan BELUM LUNAS dari siswa ini
        $tagihanBelumLunas = Tagihan::where('siswa_id', $siswaId)
            ->where('status', '!=', 'lunas')
            ->with('tagihanDetails')
            ->orderBy('created_at') // Bisa diubah jadi orderBy('total') jika perlu
            ->get();

        $jumlahDibayar = $requestData['jumlah_dibayar'];
        $sisaUang = $jumlahDibayar;

        // ✅ Validasi minimal pembayaran
        if ($jumlahDibayar <= 0) {
            flash('Jumlah pembayaran harus lebih dari Rp0.')->error();
            return back()->withInput();
        }

        $pembayaranList = [];
        $tagihanYangDibayar = []; // ✅ Untuk pesan sukses

        // ✅ 1. Cari tagihan yang nilainya SAMA dengan jumlah dibayar
        $tagihanPas = $tagihanBelumLunas->first(function ($tagihan) use ($jumlahDibayar) {
            return $tagihan->tagihanDetails->sum('jumlah_biaya') == $jumlahDibayar;
        });

        if ($tagihanPas) {
            // ✅ Bayar lunas tagihan yang nilainya pas
            $pembayaranList[] = [
                'tagihan_id' => $tagihanPas->id,
                'jumlah_dibayar' => $jumlahDibayar,
                'tanggal_bayar' => $requestData['tanggal_bayar'],
                'tanggal_konfirmasi' => now(),
                'status_konfirmasi' => 'sudah',
                'metode_pembayaran' => 'manual',
                'user_id' => auth()->id(),
                'wali_id' => $requestData['wali_id'] ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $tagihanPas->status = 'lunas';
            $tagihanPas->save();

            $tagihanYangDibayar[] = "Tagihan '{$tagihanPas->keterangan}' (Rp" . number_format($jumlahDibayar, 0, ',', '.') . ")";

            $sisaUang = 0;
        } else {
            // ✅ 2. Jika tidak ada yang pas → alokasikan ke tagihan terkecil dulu
            foreach ($tagihanBelumLunas as $tagihan) {
                if ($sisaUang <= 0) break;

                $totalTagihan = $tagihan->tagihanDetails->sum('jumlah_biaya');

                if ($sisaUang >= $totalTagihan) {
                    // Lunasi penuh
                    $pembayaranList[] = [
                        'tagihan_id' => $tagihan->id,
                        'jumlah_dibayar' => $totalTagihan,
                        'tanggal_bayar' => $requestData['tanggal_bayar'],
                        'status_konfirmasi' => 'sudah',
                        'metode_pembayaran' => 'manual',
                        'user_id' => auth()->id(),
                        'wali_id' => $requestData['wali_id'] ?? null,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];

                    $tagihan->status = 'lunas';
                    $tagihan->save();

                    $tagihanYangDibayar[] = "Tagihan '{$tagihan->keterangan}' (Rp" . number_format($totalTagihan, 0, ',', '.') . ")";

                    $sisaUang -= $totalTagihan;
                } else {
                    // Bayar sebagian
                    $pembayaranList[] = [
                        'tagihan_id' => $tagihan->id,
                        'jumlah_dibayar' => $sisaUang,
                        'tanggal_bayar' => $requestData['tanggal_bayar'],
                        'status_konfirmasi' => 'sudah',
                        'metode_pembayaran' => 'manual',
                        'user_id' => auth()->id(),
                        'wali_id' => $requestData['wali_id'] ?? null,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];

                    $tagihan->status = 'angsur';
                    $tagihan->save();

                    $tagihanYangDibayar[] = "Tagihan '{$tagihan->keterangan}' (sebagian: Rp" . number_format($sisaUang, 0, ',', '.') . ")";

                    $sisaUang = 0;
                    break;
                }
            }
        }

        // ✅ Validasi: apakah ada pembayaran yang bisa diproses?
        if (empty($pembayaranList)) {
            flash('Tidak ada tagihan yang bisa dibayar dengan jumlah tersebut.')->warning();
            return back()->withInput();
        }

        // ✅ Simpan semua pembayaran dalam 1 transaksi
        DB::transaction(function () use ($pembayaranList) {
            foreach ($pembayaranList as $data) {
                Pembayaran::create($data);
            }
        });

        // ✅ Tampilkan pesan sukses spesifik
        $pesan = "Pembayaran berhasil: " . implode(', ', $tagihanYangDibayar) . ".";
        flash($pesan)->success();

        return back();
    }

    public function show(Pembayaran $pembayaran)
    {
        auth()->user()
        ->unreadNotifications
        ->where('id', request('id'))
        ->first()
        ?->markAsRead();

        return view('operator.pembayaran_show', [
            'model' => $pembayaran,
            'route' => ['pembayaran.update', $pembayaran->id],
        ]);
    }

    public function update(Request $request, Pembayaran $pembayaran)
    {
        $pembayaran->status_konfirmasi = 'sudah';
        $pembayaran->tanggal_konfirmasi = now();
        $pembayaran->user_id = auth()->user()->id;

        $pembayaran->save();

        // update tagihan terkait
        $pembayaran->tagihan->status = 'lunas';
        $pembayaran->tagihan->save();

        flash('Data pembayaran berhasil disimpan')->success();

        return back();


    }
}
