<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePembayaranRequest;
use App\Models\Pembayaran;
use App\Models\Tagihan;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

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
        // Ambil data per siswa + periode (bulan-tahun)
        $query = DB::table('pembayarans as p')
            ->select(
                DB::raw('MAX(p.id) as id'), // ID utama untuk route show
                's.id as siswa_id',
                's.nama as nama_siswa',
                's.nisn',
                's.kelas',
                's.angkatan',
                'u.name as nama_wali',
                DB::raw('SUM(p.jumlah_dibayar) as total_dibayar'),
                DB::raw('MAX(p.tanggal_konfirmasi) as tanggal_konfirmasi'),
                DB::raw('MIN(p.status_konfirmasi) as status_konfirmasi'),
                DB::raw('DATE_FORMAT(p.tanggal_bayar, "%Y-%m") as periode')
            )
            ->join('tagihans as t', 'p.tagihan_id', '=', 't.id')
            ->join('siswas as s', 't.siswa_id', '=', 's.id')
            ->join('users as u', 'p.wali_id', '=', 'u.id')
            ->groupBy(
                's.id',
                's.nama',
                's.nisn',
                's.kelas',
                's.angkatan',
                'u.name',
                'periode'
            );

        // Filter bulan & tahun
        if (request('bulan') && request('tahun')) {
            $query->whereRaw('MONTH(p.tanggal_bayar) = ?', [request('bulan')])
                  ->whereRaw('YEAR(p.tanggal_bayar) = ?', [request('tahun')]);
        } elseif (request('tahun')) {
            $query->whereRaw('YEAR(p.tanggal_bayar) = ?', [request('tahun')]);
        }

        // Filter pencarian
        if (request('q')) {
            $query->where(function ($q) {
                $q->where('s.nama', 'like', '%' . request('q') . '%')
                  ->orWhere('s.nisn', 'like', '%' . request('q') . '%');
            });
        }

        $results = $query->orderBy('tanggal_konfirmasi', 'desc')->paginate(50);

        return view('operator.pembayaran_index', compact('results'));
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
        // Tandai notifikasi sebagai sudah dibaca jika ada
        if (auth()->check()) {
            auth()->user()
                ->unreadNotifications
                ->where('id', request('id'))
                ->first()
                ?->markAsRead();
        }

        // Ambil data siswa dari tagihan pembayaran
        $siswa = $pembayaran->tagihan?->siswa;

        // Periode pembayaran (format: YYYY-MM)
        $periode = \Carbon\Carbon::parse($pembayaran->tanggal_bayar)->format('Y-m');

        // Ambil semua pembayaran siswa di periode yang sama
        $pembayaranGroup = Pembayaran::whereHas('tagihan', function ($q) use ($siswa) {
                $q->where('siswa_id', $siswa->id);
            })
            ->whereRaw("DATE_FORMAT(tanggal_bayar, '%Y-%m') = ?", [$periode])
            ->with([
                'tagihan',
                'tagihan.siswa',
                'tagihan.tagihanDetails',
                'bankSekolah',
                'waliBank'
            ])
            ->get();

        // Hitung total pembayaran & tagihan
        $totalDibayar = $pembayaranGroup->sum('jumlah_dibayar');
        $totalTagihan = $pembayaranGroup->sum(function ($pembayaran) {
            return $pembayaran->tagihan?->tagihanDetails->sum('jumlah_biaya') ?? 0;
        });

        return view('operator.pembayaran_show', [
            'model' => $pembayaran,
            'pembayaranGroup' => $pembayaranGroup,
            'siswa' => $siswa,
            'totalDibayar' => $totalDibayar,
            'totalTagihan' => $totalTagihan,
            'route' => ['pembayaran.update', $pembayaran->id],
        ]);
    }

    public function update(Request $request, Pembayaran $pembayaran)
    {
        DB::transaction(function () use ($pembayaran) {
            // Ambil semua pembayaran dalam group yang sama
            $pembayaranGroup = Pembayaran::where('group_id', $pembayaran->group_id)->get();

            foreach ($pembayaranGroup as $item) {
                $item->status_konfirmasi = 'sudah';
                $item->tanggal_konfirmasi = now();
                $item->user_id = auth()->user()->id;
                $item->save();

                // Update status tagihan
                $item->tagihan->status = 'lunas';
                $item->tagihan->save();
            }
        });

        flash('Data pembayaran berhasil disimpan')->success();

        return back();
    }
    public function updateMultiple(Request $request)
{
    $request->validate([
        'pembayaran_ids' => 'required|array|min:1',
        'pembayaran_ids.*' => 'exists:pembayarans,id'
    ]);

    DB::transaction(function () use ($request) {
        foreach ($request->pembayaran_ids as $id) {
            $pembayaran = Pembayaran::findOrFail($id);

            // Hanya konfirmasi jika belum
            if ($pembayaran->status_konfirmasi == 'belum') {
                $pembayaran->status_konfirmasi = 'sudah';
                $pembayaran->tanggal_konfirmasi = now();
                $pembayaran->user_id = auth()->id();
                $pembayaran->save();

                // Update status tagihan jadi lunas
                $pembayaran->tagihan->status = 'lunas';
                $pembayaran->tagihan->save();
            }
        }
    });

    flash('Pembayaran terpilih berhasil dikonfirmasi.')->success();
    return back();
}
}
