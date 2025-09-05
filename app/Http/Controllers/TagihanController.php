<?php

namespace App\Http\Controllers;

use Exception;
use Carbon\Carbon;
use App\Models\Biaya;
use App\Models\Siswa;
use App\Models\Tagihan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\StoreTagihanRequest;

/**
 * Controller untuk manajemen data Tagihan Siswa.
 *
 * Mengatur CRUD Tagihan, termasuk:
 * - Menampilkan daftar tagihan terbaru per siswa
 * - Membuat tagihan massal berdasarkan filter siswa & biaya
 * - Menampilkan detail tagihan per siswa
 * - Menghapus tagihan (per item atau seluruhnya per siswa)
 *
 * @author  Umar Ulkhak
 * @date    6 September 2025
 * @updated 6 September 2025 — Tambahan notifikasi detail biaya saat hapus
 */
class TagihanController extends Controller
{
    /** Prefix path view dan route */
    private string $viewPath = 'operator.';
    private string $routePrefix = 'tagihan';

    /** Nama view blade */
    private string $viewIndex = 'tagihan_index';
    private string $viewForm  = 'tagihan_form';
    private string $viewShow  = 'tagihan_show';

    /** Status default untuk tagihan baru */
    private const STATUS_BARU = 'baru';

    // =======================
    // INDEX
    // =======================
    /**
     * Menampilkan daftar tagihan.
     *
     * Jika ada filter bulan & tahun → tampilkan semua tagihan sesuai periode.
     * Jika tanpa filter → tampilkan hanya TAGIHAN TERBARU PER SISWA.
     */
    public function index(Request $request)
    {
        $query = Tagihan::with(['user', 'siswa', 'tagihanDetails']);

        if ($request->filled(['bulan', 'tahun'])) {
            $query->whereMonth('tanggal_tagihan', $request->bulan)
                  ->whereYear('tanggal_tagihan', $request->tahun);
        } else {
            $latestPerSiswa = Tagihan::select('siswa_id', DB::raw('MAX(id) as latest_id'))
                ->groupBy('siswa_id');
            $query->whereIn('id', $latestPerSiswa->pluck('latest_id'));
        }

        if ($request->filled('q')) {
            $query->search($request->q);
        }

        $models = $query->latest()->paginate(50);

        $totalSiswa = Tagihan::distinct('siswa_id')->count('siswa_id');
        $totalLunas = Tagihan::where('status', 'lunas')->count();
        $totalBelum = Tagihan::where('status', 'baru')->count();
        $persentase = $totalSiswa > 0 ? round(($totalLunas / $totalSiswa) * 100, 1) : 0;

        return view($this->viewPath . $this->viewIndex, [
            'models'      => $models,
            'routePrefix' => $this->routePrefix,
            'title'       => 'Data Tagihan',
            'totalSiswa'  => $totalSiswa,
            'totalLunas'  => $totalLunas,
            'totalBelum'  => $totalBelum,
            'persentase'  => $persentase,
        ]);
    }

    // =======================
    // CREATE
    // =======================
    /**
     * Menampilkan form pembuatan tagihan.
     */
    public function create()
    {
        $siswa = Siswa::all();

        return view($this->viewPath . $this->viewForm, [
            'model'    => new Tagihan(),
            'method'   => 'POST',
            'route'    => $this->routePrefix . '.store',
            'button'   => 'SIMPAN',
            'title'    => 'Form Data Tagihan',
            'angkatan' => $siswa->pluck('angkatan', 'angkatan')->unique(),
            'kelas'    => $siswa->pluck('kelas', 'kelas')->unique(),
            'biaya'    => Biaya::get()->pluck('nama_biaya_full', 'id'),
        ]);
    }

    // =======================
    // STORE
    // =======================
    /**
     * Menyimpan tagihan baru (massal per siswa & biaya).
     */
    public function store(StoreTagihanRequest $request)
    {
        $data      = $request->validated();
        $biayaIds  = $data['biaya_id'] ?? [];
        $siswaList = $this->filterSiswa($data);
        $biayaList = Biaya::whereIn('id', $biayaIds)->get();

        $tanggalTagihan    = Carbon::parse($data['tanggal_tagihan']);
        $tanggalJatuhTempo = Carbon::parse($data['tanggal_jatuh_tempo']);
        $bulan             = $tanggalTagihan->format('m');
        $tahun             = $tanggalTagihan->format('Y');

        $jumlahTersimpan = 0;

        foreach ($siswaList as $siswa) {
            foreach ($biayaList as $biaya) {
                if ($this->cekDuplikat($siswa->id, $biaya->nama, $bulan, $tahun)) {
                    continue;
                }

                $tagihan = Tagihan::create([
                    'siswa_id'            => $siswa->id,
                    'angkatan'            => $siswa->angkatan,
                    'kelas'               => $siswa->kelas,
                    'tanggal_tagihan'     => $tanggalTagihan,
                    'tanggal_jatuh_tempo' => $tanggalJatuhTempo,
                    'keterangan'          => $data['keterangan'] ?? null,
                    'status'              => self::STATUS_BARU,
                ]);

                $tagihan->tagihanDetails()->create([
                    'nama_biaya'   => $biaya->nama,
                    'jumlah_biaya' => $biaya->jumlah,
                ]);

                $jumlahTersimpan++;
            }
        }

        return redirect()
            ->route($this->routePrefix . '.index')
            ->with('success', "Tagihan berhasil dibuat: {$jumlahTersimpan} tagihan baru untuk " . count($siswaList) . " siswa.");
    }

    // =======================
    // SHOW
    // =======================
    /**
     * Menampilkan detail tagihan per siswa.
     */
    public function show(Request $request, int $id)
    {
        $siswa = Siswa::findOrFail($id);

        $query = Tagihan::with('tagihanDetails')
            ->where('siswa_id', $siswa->id);

        if ($bulan = $request->input('bulan')) {
            $query->whereMonth('tanggal_tagihan', $bulan);
        }

        if ($tahun = $request->input('tahun')) {
            $tahun = strlen($tahun) === 2 ? '20' . $tahun : $tahun;
            $query->whereYear('tanggal_tagihan', $tahun);
        }

        return view($this->viewPath . $this->viewShow, [
            'siswa'   => $siswa,
            'tagihan' => $query->orderBy('tanggal_tagihan', 'desc')->get(),
            'title'   => "Daftar Tagihan {$siswa->nama}",
        ]);
    }

    // =======================
    // DESTROY (HAPUS SATU)
    // =======================
    /**
     * Menghapus satu tagihan beserta detailnya.
     *
     * Pesan notifikasi mencantumkan nama siswa, NISN, jumlah detail, dan daftar biaya.
     */
    public function destroy(Tagihan $tagihan)
    {
        try {
            $siswa = $tagihan->siswa;
            $detailList   = $tagihan->tagihanDetails()->pluck('nama_biaya')->toArray();
            $jumlahDetail = count($detailList);
            $namaBiaya    = $jumlahDetail > 0 ? implode(', ', $detailList) : '—';

            $tagihan->tagihanDetails()->delete();
            $tagihan->delete();

            return redirect()
                ->back()
                ->with(
                    'success',
                    "Tagihan siswa {$siswa->nama} ({$siswa->nisn}) berhasil dihapus ({$jumlahDetail} detail: {$namaBiaya})."
                );
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Gagal menghapus tagihan: ' . $e->getMessage());
        }
    }

    // =======================
    // DESTROY SISWA (HAPUS SEMUA)
    // =======================
    /**
     * Menghapus SEMUA tagihan seorang siswa beserta detailnya.
     *
     * Pesan notifikasi mencantumkan nama siswa, NISN, jumlah detail, dan daftar biaya.
     */
    public function destroySiswa(int $siswaId)
    {
        try {
            $siswa = Siswa::findOrFail($siswaId);

            $tagihans = Tagihan::where('siswa_id', $siswaId)->get();
            $jumlahDetail = 0;
            $allBiaya = [];

            foreach ($tagihans as $tagihan) {
                $detailList = $tagihan->tagihanDetails()->pluck('nama_biaya')->toArray();
                $jumlahDetail += count($detailList);
                $allBiaya = array_merge($allBiaya, $detailList);

                $tagihan->tagihanDetails()->delete();
                $tagihan->delete();
            }

            $biayaText = $jumlahDetail > 0 ? implode(', ', $allBiaya) : '—';

            return redirect()
                ->route($this->routePrefix . '.index')
                ->with(
                    'success',
                    "Semua tagihan siswa {$siswa->nama} ({$siswa->nisn}) berhasil dihapus ({$jumlahDetail} detail: {$biayaText})."
                );
        } catch (Exception $e) {
            return redirect()
                ->route($this->routePrefix . '.index')
                ->with('error', 'Gagal menghapus tagihan: ' . $e->getMessage());
        }
    }

    // =======================
    // HELPER PRIVATE
    // =======================
    /**
     * Memfilter siswa berdasarkan kelas dan/atau angkatan.
     */
    private function filterSiswa(array $data): \Illuminate\Database\Eloquent\Collection
    {
        return Siswa::query()
            ->when(!empty($data['kelas']), fn($q) => $q->where('kelas', $data['kelas']))
            ->when(!empty($data['angkatan']), fn($q) => $q->where('angkatan', $data['angkatan']))
            ->get();
    }

    /**
     * Mengecek duplikasi tagihan berdasarkan:
     * - Siswa
     * - Nama biaya
     * - Bulan & Tahun tagihan
     */
    private function cekDuplikat(int $siswaId, string $namaBiaya, string $bulan, string $tahun): bool
    {
        return Tagihan::where('siswa_id', $siswaId)
            ->whereMonth('tanggal_tagihan', $bulan)
            ->whereYear('tanggal_tagihan', $tahun)
            ->whereHas('tagihanDetails', fn($q) => $q->where('nama_biaya', $namaBiaya))
            ->exists();
    }
}
