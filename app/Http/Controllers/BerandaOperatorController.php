<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Siswa;
use App\Models\Tagihan;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class BerandaOperatorController extends Controller
{
    public function index()
    {
        $bulan = request('bulan', Carbon::now()->format('m'));
        $tahun = request('tahun', Carbon::now()->format('Y'));

        $stats = $this->calculateStats($bulan, $tahun);
        $kelasStats = $this->getKelasStatsOptimized($bulan, $tahun);
        $recentPayments = $this->getRecentPaymentsOptimized();

        return view('operator.beranda_index', [
            'title' => 'Dashboard Operator',
            'stats' => $stats,
            'kelasStats' => $kelasStats,
            'recentPayments' => $recentPayments,
        ]);
    }

    private function calculateStats($bulan, $tahun)
    {
        // Total siswa
        $totalSiswa = Siswa::count();

        // Total pembayaran bulan ini (lunas)
        $pembayaranBulanIni = DB::table('tagihans as t')
            ->join('tagihan_details as td', 't.id', '=', 'td.tagihan_id')
            ->where('t.status', 'lunas')
            ->whereMonth('t.tanggal_tagihan', $bulan)
            ->whereYear('t.tanggal_tagihan', $tahun)
            ->sum('td.jumlah_biaya');

        // Total tagihan & status bulan ini
        $tagihanBulanIni = DB::table('tagihans')
            ->select(
                DB::raw('SUM(CASE WHEN status = "lunas" THEN 1 ELSE 0 END) as total_lunas'),
                DB::raw('SUM(CASE WHEN status = "baru" THEN 1 ELSE 0 END) as total_pending'),
                DB::raw('SUM(CASE WHEN status != "lunas" THEN 1 ELSE 0 END) as total_belum_bayar'),
                DB::raw('COUNT(*) as total_tagihan')
            )
            ->whereMonth('tanggal_tagihan', $bulan)
            ->whereYear('tanggal_tagihan', $tahun)
            ->first();

        $totalLunas = $tagihanBulanIni->total_lunas ?? 0;
        $totalPending = $tagihanBulanIni->total_pending ?? 0;
        $totalBelumBayar = $tagihanBulanIni->total_belum_bayar ?? 0;
        $totalTagihan = $tagihanBulanIni->total_tagihan ?? 0;

        $tingkatPembayaran = $totalTagihan > 0 ? round(($totalLunas / $totalTagihan) * 100, 1) : 0;

        // Data bulan lalu
        $bulanLalu = Carbon::create($tahun, $bulan)->subMonth();
        $bln = $bulanLalu->format('m');
        $thn = $bulanLalu->format('Y');

        $tagihanBulanLalu = DB::table('tagihans')
            ->select(
                DB::raw('SUM(CASE WHEN status = "lunas" THEN 1 ELSE 0 END) as total_lunas'),
                DB::raw('COUNT(*) as total_tagihan')
            )
            ->whereMonth('tanggal_tagihan', $bln)
            ->whereYear('tanggal_tagihan', $thn)
            ->first();

        $lunasLalu = $tagihanBulanLalu->total_lunas ?? 0;
        $tagihanLalu = $tagihanBulanLalu->total_tagihan ?? 0;
        $persenLalu = $tagihanLalu > 0 ? round(($lunasLalu / $tagihanLalu) * 100, 1) : 0;
        $peningkatanBulanLalu = $tingkatPembayaran - $persenLalu;

        // Pertumbuhan pembayaran
        $pembayaranBulanLalu = DB::table('tagihans as t')
            ->join('tagihan_details as td', 't.id', '=', 'td.tagihan_id')
            ->where('t.status', 'lunas')
            ->whereMonth('t.tanggal_tagihan', $bln)
            ->whereYear('t.tanggal_tagihan', $thn)
            ->sum('td.jumlah_biaya');

        $pertumbuhan = $pembayaranBulanLalu == 0
            ? ($pembayaranBulanIni > 0 ? 100 : 0)
            : round((($pembayaranBulanIni - $pembayaranBulanLalu) / $pembayaranBulanLalu) * 100, 1);

        return [
            'total_siswa' => $totalSiswa,
            'siswa_baru' => 0,
            'pembayaran_bulan_ini' => (int) $pembayaranBulanIni,
            'pertumbuhan_bulan_lalu' => $pertumbuhan,
            'tingkat_pembayaran' => $tingkatPembayaran,
            'peningkatan_bulan_lalu' => $peningkatanBulanLalu,
            'tunggakan' => $totalBelumBayar,
            'tagihan_belum_bayar' => $totalBelumBayar,
            'total_lunas' => $totalLunas,
            'total_pending' => $totalPending,
            'total_belum_bayar' => $totalBelumBayar,
        ];
    }

    private function getKelasStatsOptimized($bulan, $tahun)
    {
        // Ambil data per kelas dengan 1 query
        $actual = DB::table('siswas')
            ->select('siswas.kelas') // ✅ Tambahkan prefix 'siswas.'
            ->selectRaw('COUNT(*) as jumlah_siswa')
            ->selectRaw('SUM(CASE WHEN t.status = "lunas" THEN 1 ELSE 0 END) as lunas')
            ->selectRaw('SUM(CASE WHEN t.status = "baru" THEN 1 ELSE 0 END) as pending')
            ->selectRaw('SUM(CASE WHEN t.status NOT IN ("lunas", "baru") AND t.status IS NOT NULL THEN 1 ELSE 0 END) as belum_bayar')
            ->leftJoin('tagihans as t', function ($join) use ($bulan, $tahun) {
                $join->on('siswas.id', '=', 't.siswa_id')
                    ->whereRaw('MONTH(t.tanggal_tagihan) = ?', [$bulan])
                    ->whereRaw('YEAR(t.tanggal_tagihan) = ?', [$tahun]);
            })
            ->groupBy('siswas.kelas') // ✅ Prefix di sini juga
            ->orderBy('siswas.kelas') // ✅ Dan di sini
            ->get()
            ->keyBy('kelas');

        $kelasAll = ['VII', 'VIII', 'IX'];
        $result = [];

        foreach ($kelasAll as $kelas) {
            if (isset($actual[$kelas])) {
                $data = $actual[$kelas];
                $result[] = [
                    'nama' => $kelas,
                    'jumlah_siswa' => (int) $data->jumlah_siswa,
                    'lunas' => (int) $data->lunas,
                    'pending' => (int) $data->pending,
                    'belum_bayar' => (int) $data->belum_bayar,
                ];
            } else {
                $jumlah = Siswa::where('kelas', $kelas)->count();
                $result[] = [
                    'nama' => $kelas,
                    'jumlah_siswa' => $jumlah,
                    'lunas' => 0,
                    'pending' => 0,
                    'belum_bayar' => 0,
                ];
            }
        }

        return $result;
    }

    private function getRecentPaymentsOptimized()
    {
        // Ambil 5 pembayaran terbaru + total biaya dalam 1 query
        $payments = DB::table('tagihans as t')
            ->select(
                't.id',
                't.siswa_id',
                't.status',
                't.tanggal_tagihan',
                't.created_at',
                DB::raw('SUM(td.jumlah_biaya) as total_biaya')
            )
            ->join('tagihan_details as td', 't.id', '=', 'td.tagihan_id')
            ->where('t.status', 'lunas')
            ->groupBy('t.id', 't.siswa_id', 't.status', 't.tanggal_tagihan', 't.created_at')
            ->orderBy('t.created_at', 'desc')
            ->take(5)
            ->get();

        // Ambil data siswa dalam 1 query
        $siswaIds = $payments->pluck('siswa_id')->unique()->toArray();
        $siswas = Siswa::whereIn('id', $siswaIds)->get()->keyBy('id');

        // Gabungkan data siswa ke pembayaran
        foreach ($payments as $payment) {
            $payment->siswa = $siswas[$payment->siswa_id] ?? null;
        }

        return $payments;
    }

}
