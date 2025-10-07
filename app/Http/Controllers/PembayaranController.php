<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePembayaranRequest;
use App\Models\Pembayaran;
use App\Models\Tagihan;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class PembayaranController extends Controller
{
    public function index()
    {
        // Ambil data per siswa (tanpa grup periode)
        $query = DB::table('pembayarans as p')
            ->select(
                DB::raw('MAX(p.id) as id'),
                's.id as siswa_id',
                's.nama as nama_siswa',
                's.nisn',
                's.kelas',
                's.angkatan',
                'u.name as nama_wali',
                DB::raw('SUM(p.jumlah_dibayar) as total_dibayar'),
                DB::raw('MAX(p.tanggal_konfirmasi) as tanggal_konfirmasi'),
                DB::raw('MIN(p.status_konfirmasi) as status_konfirmasi')
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
                'u.name'
            );

        if (request('bulan') && request('tahun')) {
            $query->whereRaw('MONTH(p.tanggal_bayar) = ?', [request('bulan')])
                  ->whereRaw('YEAR(p.tanggal_bayar) = ?', [request('tahun')]);
        } elseif (request('tahun')) {
            $query->whereRaw('YEAR(p.tanggal_bayar) = ?', [request('tahun')]);
        }

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
        $tagihanUtama = Tagihan::findOrFail($requestData['tagihan_id']);
        $siswaId = $tagihanUtama->siswa_id;
        $requestData['wali_id'] = $tagihanUtama->siswa->wali_id;

        $tagihanBelumLunas = Tagihan::where('siswa_id', $siswaId)
            ->where('status', '!=', 'lunas')
            ->with('tagihanDetails')
            ->orderBy('created_at')
            ->get();

        $jumlahDibayar = $requestData['jumlah_dibayar'];
        $sisaUang = $jumlahDibayar;

        if ($jumlahDibayar <= 0) {
            flash('Jumlah pembayaran harus lebih dari Rp0.')->error();
            return back()->withInput();
        }

        $pembayaranList = [];
        $tagihanYangDibayar = [];

        $tagihanPas = $tagihanBelumLunas->first(function ($tagihan) use ($jumlahDibayar) {
            return $tagihan->tagihanDetails->sum('jumlah_biaya') == $jumlahDibayar;
        });

        if ($tagihanPas) {
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
            foreach ($tagihanBelumLunas as $tagihan) {
                if ($sisaUang <= 0) break;
                $totalTagihan = $tagihan->tagihanDetails->sum('jumlah_biaya');

                if ($sisaUang >= $totalTagihan) {
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

        if (empty($pembayaranList)) {
            flash('Tidak ada tagihan yang bisa dibayar dengan jumlah tersebut.')->warning();
            return back()->withInput();
        }

        DB::transaction(function () use ($pembayaranList) {
            foreach ($pembayaranList as $data) {
                Pembayaran::create($data);
            }
        });

        $pesan = "Pembayaran berhasil: " . implode(', ', $tagihanYangDibayar) . ".";
        flash($pesan)->success();
        return back();
    }

    public function show(Pembayaran $pembayaran)
    {
        if (auth()->check()) {
            auth()->user()
                ->unreadNotifications
                ->where('id', request('id'))
                ->first()
                ?->markAsRead();
        }

        $siswa = $pembayaran->tagihan?->siswa;

        // Ambil SEMUA pembayaran dari siswa ini
        $pembayaranGroup = Pembayaran::whereHas('tagihan', function ($q) use ($siswa) {
                $q->where('siswa_id', $siswa->id);
            })
            ->with([
                'tagihan',
                'tagihan.siswa',
                'tagihan.tagihanDetails',
                'bankSekolah',
                'waliBank'
            ])
            ->orderBy('tanggal_bayar', 'desc')
            ->get();

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
            'route' => ['pembayaran.update.multiple'],
        ]);
    }

    public function update(Request $request, Pembayaran $pembayaran)
    {
        flash('Gunakan fitur konfirmasi selektif.')->warning();
        return back();
    }

    public function updateMultiple(Request $request)
    {
        // Tambahkan import: use Illuminate\Http\Request;
        $request->validate([
            'pembayaran_ids' => 'required|array|min:1',
            'pembayaran_ids.*' => 'exists:pembayarans,id'
        ]);

        DB::transaction(function () use ($request) {
            foreach ($request->pembayaran_ids as $id) {
                $pembayaran = Pembayaran::findOrFail($id);
                if ($pembayaran->status_konfirmasi == 'belum') {
                    $pembayaran->status_konfirmasi = 'sudah';
                    $pembayaran->tanggal_konfirmasi = now();
                    $pembayaran->user_id = auth()->id();
                    $pembayaran->save();

                    $pembayaran->tagihan->status = 'lunas';
                    $pembayaran->tagihan->save();
                }
            }
        });

        flash('Pembayaran terpilih berhasil dikonfirmasi.')->success();
        return back();
    }
}
