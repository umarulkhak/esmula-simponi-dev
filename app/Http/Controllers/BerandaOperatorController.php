<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Siswa;
use App\Models\Tagihan;
use App\Models\Biaya;
use Carbon\Carbon;

/**
 * Controller untuk menampilkan dashboard operator.
 *
 * Menyediakan data statistik, grafik, dan aktivitas terbaru untuk ditampilkan
 * di halaman beranda operator.
 *
 * @author  Umar Ulkhak
 * @date    5 April 2025
 */
class BerandaOperatorController extends Controller
{
    /**
     * Menampilkan halaman beranda operator dengan data statistik.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Ambil filter bulan & tahun dari request (default: bulan & tahun sekarang)
        $bulan = request('bulan', Carbon::now()->format('m'));
        $tahun = request('tahun', Carbon::now()->format('Y'));

        // Hitung statistik
        $stats = $this->calculateStats($bulan, $tahun);

        // Ambil data per kelas
        $kelasStats = $this->getKelasStats($bulan, $tahun);

        // Ambil pembayaran terbaru
        $recentPayments = $this->getRecentPayments();

        // Ambil aktivitas terbaru
        $recentActivities = $this->getRecentActivities();

        return view('operator.beranda_index', [
            'title'           => 'Dashboard Operator',
            'stats'           => $stats,
            'kelasStats'      => $kelasStats,
            'recentPayments'  => $recentPayments,
            'recentActivities' => $recentActivities,
        ]);
    }

    /**
     * Menghitung statistik dashboard (total siswa, pembayaran, dll).
     *
     * @param  string  $bulan
     * @param  string  $tahun
     * @return array
     */
    private function calculateStats($bulan, $tahun)
    {
        // Total siswa
        $totalSiswa = Siswa::count();
        $siswaBulanLalu = Siswa::whereMonth('created_at', Carbon::now()->subMonth()->month)
                               ->whereYear('created_at', Carbon::now()->subMonth()->year)
                               ->count();
        $diffSiswa = $totalSiswa - $siswaBulanLalu;

        // Tagihan lunas bulan ini
        $totalLunas = Tagihan::where('status', 'lunas')
                            ->whereMonth('tanggal_tagihan', $bulan)
                            ->whereYear('tanggal_tagihan', $tahun)
                            ->count();
        $lunasBulanLalu = Tagihan::where('status', 'lunas')
                                ->whereMonth('tanggal_tagihan', Carbon::now()->subMonth()->month)
                                ->whereYear('tanggal_tagihan', Carbon::now()->subMonth()->year)
                                ->count();
        $diffLunas = $totalLunas - $lunasBulanLalu;

        // Tagihan belum bayar
        $totalBelum = Tagihan::where('status', '!=', 'lunas')
                            ->whereMonth('tanggal_tagihan', $bulan)
                            ->whereYear('tanggal_tagihan', $tahun)
                            ->count();
        $belumBulanLalu = Tagihan::where('status', '!=', 'lunas')
                                ->whereMonth('tanggal_tagihan', Carbon::now()->subMonth()->month)
                                ->whereYear('tanggal_tagihan', Carbon::now()->subMonth()->year)
                                ->count();
        $diffBelum = $totalBelum - $belumBulanLalu;

        // Tingkat pembayaran
        $totalTagihan = Tagihan::whereMonth('tanggal_tagihan', $bulan)
                              ->whereYear('tanggal_tagihan', $tahun)
                              ->count();
        $persentase = $totalTagihan > 0 ? round(($totalLunas / $totalTagihan) * 100, 1) : 0;

        $totalTagihanBulanLalu = Tagihan::whereMonth('tanggal_tagihan', Carbon::now()->subMonth()->month)
                                       ->whereYear('tanggal_tagihan', Carbon::now()->subMonth()->year)
                                       ->count();
        $persentaseBulanLalu = $totalTagihanBulanLalu > 0 ? round(($lunasBulanLalu / $totalTagihanBulanLalu) * 100, 1) : 0;
        $diffPersen = $persentase - $persentaseBulanLalu;

        return [
            'total_siswa'           => $totalSiswa,
            'siswa_baru'            => $diffSiswa,
            'pembayaran_bulan_ini'  => $this->calculateTotalPembayaran($bulan, $tahun),
            'pertumbuhan_bulan_lalu' => $this->calculateGrowthPercentage($bulan, $tahun),
            'tingkat_pembayaran'    => $persentase,
            'peningkatan_bulan_lalu' => $diffPersen,
            'tunggakan'             => $totalBelum,
            'tagihan_belum_bayar'   => $totalBelum,
            'total_lunas'           => $totalLunas,
            'total_pending'         => Tagihan::where('status', 'baru')
                                              ->whereMonth('tanggal_tagihan', $bulan)
                                              ->whereYear('tanggal_tagihan', $tahun)
                                              ->count(),
            'total_belum_bayar'     => $totalBelum,
        ];
    }

    /**
     * Menghitung total pembayaran bulan ini.
     *
     * @param  string  $bulan
     * @param  string  $tahun
     * @return int
     */
    private function calculateTotalPembayaran($bulan, $tahun)
    {
        return Tagihan::where('status', 'lunas')
                     ->whereMonth('tanggal_tagihan', $bulan)
                     ->whereYear('tanggal_tagihan', $tahun)
                     ->with('tagihanDetails')
                     ->get()
                     ->sum(function ($tagihan) {
                         return $tagihan->tagihanDetails->sum('jumlah_biaya');
                     });
    }

    /**
     * Menghitung persentase pertumbuhan pembayaran dari bulan lalu.
     *
     * @param  string  $bulan
     * @param  string  $tahun
     * @return float
     */
    private function calculateGrowthPercentage($bulan, $tahun)
    {
        $bulanLalu = Carbon::create($tahun, $bulan)->subMonth();
        $totalBulanIni = $this->calculateTotalPembayaran($bulan, $tahun);
        $totalBulanLalu = $this->calculateTotalPembayaran($bulanLalu->format('m'), $bulanLalu->format('Y'));

        if ($totalBulanLalu == 0) {
            return $totalBulanIni > 0 ? 100 : 0;
        }

        return round((($totalBulanIni - $totalBulanLalu) / $totalBulanLalu) * 100, 1);
    }

    /**
     * Mengambil data statistik per kelas.
     *
     * @param  string  $bulan
     * @param  string  $tahun
     * @return array
     */
    private function getKelasStats($bulan, $tahun)
    {
        $kelas = ['VII', 'VIII', 'IX'];
        $result = [];

        foreach ($kelas as $namaKelas) {
            $siswaKelas = Siswa::where('kelas', $namaKelas)->get();
            $jumlahSiswa = $siswaKelas->count();

            $lunas = 0;
            $pending = 0;
            $belumBayar = 0;

            foreach ($siswaKelas as $siswa) {
                $tagihan = Tagihan::where('siswa_id', $siswa->id)
                                 ->whereMonth('tanggal_tagihan', $bulan)
                                 ->whereYear('tanggal_tagihan', $tahun)
                                 ->first();

                if ($tagihan) {
                    if ($tagihan->status === 'lunas') {
                        $lunas++;
                    } elseif ($tagihan->status === 'baru') {
                        $pending++;
                    } else {
                        $belumBayar++;
                    }
                }
            }

            $result[] = [
                'nama' => $namaKelas,
                'jumlah_siswa' => $jumlahSiswa,
                'lunas' => $lunas,
                'pending' => $pending,
                'belum_bayar' => $belumBayar,
            ];
        }

        return $result;
    }

    /**
     * Mengambil daftar pembayaran terbaru.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    private function getRecentPayments()
    {
        return Tagihan::with('siswa')
                     ->where('status', 'lunas')
                     ->orderBy('created_at', 'desc')
                     ->take(5)
                     ->get()
                     ->map(function ($tagihan) {
                         $tagihan->total_biaya = $tagihan->tagihanDetails->sum('jumlah_biaya');
                         return $tagihan;
                     });
    }

    /**
     * Mengambil daftar aktivitas terbaru.
     *
     * @return array
     */
    private function getRecentActivities()
    {
        // Simulasi data aktivitas (bisa diganti dengan model ActivityLog)
        return collect([
            [
                'title' => 'Pembayaran diterima',
                'description' => 'Ahmad Rizki - XII IPA 1',
                'type' => 'payment',
                'created_at' => Carbon::now()->subMinutes(5),
            ],
            [
                'title' => 'Siswa baru terdaftar',
                'description' => 'Maya Putri - X MIPA 2',
                'type' => 'student',
                'created_at' => Carbon::now()->subHours(2),
            ],
            [
                'title' => 'Reminder dikirim',
                'description' => '5 siswa dengan tunggakan',
                'type' => 'reminder',
                'created_at' => Carbon::now()->subDays(1),
            ],
            [
                'title' => 'Tagihan jatuh tempo',
                'description' => 'SPP Januari 2024',
                'type' => 'due',
                'created_at' => Carbon::now()->subDays(3),
            ],
        ]);
    }
}
